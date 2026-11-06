<?php

namespace App\Console\Commands;

use App\Contracts\AppointmentMonitoringProvider;
use App\Models\MonitoringTask;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RunVisaMonitoringCommand extends Command
{
    protected $signature = 'visa:monitor {--force : Force check regardless of next_check_at timestamp}';

    protected $description = 'Execute scheduled monitoring checks on USVisaScheduling portal for active applicants';

    public function handle(AppointmentMonitoringProvider $provider): int
    {
        $this->info('Starting U.S. Visa Appointment Monitoring cycle ('.$provider->getProviderName().')...');

        $query = MonitoringTask::with(['applicant.preference', 'user', 'schedule'])
            ->where('status', 'active');

        if (! $this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('next_check_at')
                    ->orWhere('next_check_at', '<=', Carbon::now());
            });
        }

        $tasks = $query->get();

        if ($tasks->isEmpty()) {
            $this->line('No tasks currently due for monitoring check.');

            return Command::SUCCESS;
        }

        $this->info("Found {$tasks->count()} task(s) due for check.");

        foreach ($tasks as $task) {
            $category = $task->applicant->preference->visa_category ?? 'N/A';
            $this->line("Checking task #{$task->id} for {$task->applicant->full_name} ({$category})...");
            $result = $provider->checkAvailability($task);
            $msg = $result['message'] ?? ($result['status'] ?? 'done');
            $this->info(" -> Status: {$result['status']} | {$msg}");
        }

        $this->info('Monitoring cycle complete.');

        return Command::SUCCESS;
    }
}
