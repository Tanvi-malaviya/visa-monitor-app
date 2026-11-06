@extends('layouts.app')

@section('title', 'Admin Command Center - Sathwara InfoTech')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Top Admin Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--accent-cyan); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 0.25rem;">
                <span class="pulse-dot"></span> Central Laravel Server Host
            </div>
            <h2 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.4px;">Command Center & Schedule Manager</h2>
            <p style="color: var(--text-secondary); font-size: 0.88rem;">Oversee all active background monitoring workers, applicant sessions, and automated checks.</p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <!-- Active Automation Engine Badge -->
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(59, 130, 246, 0.12); border: 1px solid var(--border-accent); padding: 0.5rem 0.9rem; border-radius: 10px; font-size: 0.8rem; font-weight: 700; color: var(--accent-cyan);">
                <span class="pulse-dot"></span>
                <span>Engine: ⚡ Playwright Browser Worker</span>
            </div>

            
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
        <div class="glass-card" style="border-left: 4px solid var(--accent-blue);">
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Total Customers</div>
            <div style="font-size: 1.85rem; font-weight: 800; margin-top: 0.4rem; color: var(--text-primary);">{{ $customersCount }}</div>
            <div style="font-size: 0.75rem; color: var(--accent-cyan); margin-top: 0.3rem;">All registered applicants</div>
        </div>

        <div class="glass-card" style="border-left: 4px solid #10b981;">
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Active Background Workers</div>
            <div style="font-size: 1.85rem; font-weight: 800; margin-top: 0.4rem; color: #34d399;">{{ $activeJobsCount }}</div>
            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.3rem;">Automated cron jobs executing</div>
        </div>

        <div class="glass-card" style="border-left: 4px solid var(--accent-amber);">
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Pending MFA / OTP Challenges</div>
            <div style="font-size: 1.85rem; font-weight: 800; margin-top: 0.4rem; color: #fbbf24;">{{ $challengesCount }}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.3rem;">Awaiting customer intervention</div>
        </div>

        <div class="glass-card" style="border-left: 4px solid var(--accent-cyan);">
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Detected Open Slots</div>
            <div style="font-size: 1.85rem; font-weight: 800; margin-top: 0.4rem; color: var(--accent-cyan);">{{ $slotsFoundCount }}</div>
            <div style="font-size: 0.75rem; color: #34d399; margin-top: 0.3rem;">Available for confirmation</div>
        </div>
    </div>

    

    <!-- Active Monitoring Tasks Table -->
    <div class="glass-card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <div>
                <h3 style="font-size: 1.25rem; font-weight: 800;">Configured Schedules & Monitoring Tasks</h3>
                <p style="color: var(--text-secondary); font-size: 0.85rem;">Active customer session status, check frequencies, and intervention states.</p>
            </div>
            <a href="{{ route('admin.customers') }}" class="btn btn-outline btn-sm">View All Profiles</a>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-glass); color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">
                        <th style="padding: 0.75rem 1rem;">Customer & Applicant</th>
                        <th style="padding: 0.75rem 1rem;">Visa Category</th>
                        <th style="padding: 0.75rem 1rem;">Consulates</th>
                        <th style="padding: 0.75rem 1rem;">Interval</th>
                        <th style="padding: 0.75rem 1rem;">Last Check</th>
                        <th style="padding: 0.75rem 1rem;">Status</th>
                        <th style="padding: 0.75rem 1rem; text-align: right;">Worker Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $t)
                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.04); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1rem;">
                                <div style="font-weight: 700; color: var(--text-primary);">{{ $t->applicant->full_name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $t->user->email }}</div>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="color: var(--accent-cyan); font-weight: 600;">{{ $t->applicant->preference->visa_category ?? 'B1/B2' }}</span>
                            </td>
                            <td style="padding: 1rem; font-size: 0.82rem; color: var(--text-secondary);">
                                {{ implode(', ', $t->applicant->preference->preferred_locations ?? []) }}
                            </td>
                            <td style="padding: 1rem; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">
                                {{ $t->schedule->interval_minutes ?? 5 }}m
                            </td>
                            <td style="padding: 1rem; font-size: 0.82rem; color: var(--text-muted);">
                                {{ $t->last_check_at ? $t->last_check_at->diffForHumans() : 'Never' }}
                            </td>
                            <td style="padding: 1rem;">
                                @if($t->status === 'active')
                                    <span class="status-badge badge-active"><span class="pulse-dot"></span> Active</span>
                                @elseif($t->status === 'awaiting_action')
                                    <span class="status-badge badge-warning"><span class="pulse-dot"></span> Action Required</span>
                                @elseif($t->status === 'slot_found')
                                    <span class="status-badge badge-success"><span class="pulse-dot"></span> Slot Found</span>
                                @elseif($t->status === 'completed')
                                    <span class="status-badge badge-active" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">✓ Confirmed</span>
                                @else
                                    <span class="status-badge badge-paused">Paused</span>
                                @endif
                            </td>
                            <td style="padding: 1rem; text-align: right;">
                                <div style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                    <form method="POST" action="{{ route('admin.jobs.toggle', $t->id) }}" style="display: inline;">
                                        @csrf
                                        @if($t->status === 'active')
                                            <button type="submit" class="btn btn-outline btn-sm" style="border-color: rgba(245, 158, 11, 0.4); color: #fbbf24;">Pause</button>
                                        @else
                                            <button type="submit" class="btn btn-emerald btn-sm">Resume</button>
                                        @endif
                                    </form>

                                    
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Central Activity Audit Logs -->
    <div class="glass-card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <div>
                <h3 style="font-size: 1.25rem; font-weight: 800;">Central Activity & Worker Audit Trail</h3>
                <p style="color: var(--text-secondary); font-size: 0.85rem;">System-wide log of background scans, reauthentication checks, and notifications.</p>
            </div>
            <span class="status-badge badge-active" style="font-size: 0.72rem;">Real-Time Feed</span>
        </div>

        <div style="max-height: 400px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.6rem;">
            @foreach($recentLogs as $log)
                <div style="background: rgba(0,0,0,0.3); border-radius: 8px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; border-left: 3px solid {{ $log->level === 'success' ? '#10b981' : ($log->level === 'warning' ? '#f59e0b' : ($log->level === 'error' ? '#ef4444' : '#3b82f6')) }};">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; font-weight: 700; color: {{ $log->level === 'success' ? '#34d399' : ($log->level === 'warning' ? '#fbbf24' : ($log->level === 'error' ? '#f87171' : '#60a5fa')) }};">
                            [{{ $log->event_type }}]
                        </span>
                        <span style="font-size: 0.85rem; color: var(--text-secondary);">
                            {{ $log->message }}
                        </span>
                    </div>
                    <span style="font-size: 0.72rem; color: var(--text-muted); white-space: nowrap;">
                        {{ $log->created_at->diffForHumans() }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
