<?php

namespace App\Providers;

use App\Contracts\AppointmentMonitoringProvider;
use App\Models\SystemSetting;
use App\Services\Providers\PlaywrightAppointmentMonitoringProvider;
use App\Services\Providers\SimulatedAppointmentMonitoringProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind primary automation engine: Playwright Browser Worker
        $this->app->singleton(AppointmentMonitoringProvider::class, function ($app) {
            return new PlaywrightAppointmentMonitoringProvider;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
