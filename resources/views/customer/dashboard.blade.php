@extends('layouts.app')

@section('title', 'My Visa Appointment Status')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Top Hero Status Card -->
    <div class="glass-card" style="border: 1px solid rgba(59, 130, 246, 0.25); background: linear-gradient(135deg, rgba(17, 24, 39, 0.85) 0%, rgba(15, 23, 42, 0.95) 100%);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
            
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div style="position: relative;">
                    <div style="width: 64px; height: 64px; border-radius: 16px; background: rgba(15, 23, 42, 0.9); border: 2px solid {{ $task->status === 'active' ? '#10b981' : ($task->status === 'awaiting_action' ? '#f59e0b' : ($task->status === 'slot_found' ? '#06b6d4' : '#64748b')) }}; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px {{ $task->status === 'active' ? 'rgba(16, 185, 129, 0.3)' : ($task->status === 'awaiting_action' ? 'rgba(245, 158, 11, 0.3)' : 'rgba(0,0,0,0.5)') }};">
                        @if($task->status === 'active')
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        @elseif($task->status === 'awaiting_action')
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        @elseif($task->status === 'slot_found')
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        @elseif($task->status === 'completed')
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        @else
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="10" y1="15" x2="10" y2="9"></line><line x1="14" y1="15" x2="14" y2="9"></line></svg>
                        @endif
                    </div>
                    @if($task->status === 'active')
                        <span style="position: absolute; bottom: -4px; right: -4px; width: 14px; height: 14px; border-radius: 50%; background: #10b981; border: 2px solid #0f172a; box-shadow: 0 0 10px #10b981;"></span>
                    @endif
                </div>

                <div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
                        <h2 style="font-size: 1.45rem; font-weight: 800; letter-spacing: -0.3px;">{{ $applicant->full_name }}</h2>
                        
                        @if($task->status === 'active')
                            <span class="status-badge badge-active"><span class="pulse-dot"></span> 24/7 Monitoring Active</span>
                        @elseif($task->status === 'awaiting_action')
                            <span class="status-badge badge-warning"><span class="pulse-dot"></span> Action Required (OTP / MFA)</span>
                        @elseif($task->status === 'slot_found')
                            <span class="status-badge badge-success"><span class="pulse-dot"></span> Slot Available!</span>
                        @elseif($task->status === 'completed')
                            <span class="status-badge badge-active" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">✓ Appointment Booked</span>
                        @else
                            <span class="status-badge badge-paused">Monitoring Paused</span>
                        @endif
                    </div>

                    <div style="display: flex; align-items: center; gap: 1.5rem; font-size: 0.85rem; color: var(--text-secondary); flex-wrap: wrap;">
                        <div><strong>Visa Category:</strong> <span style="color: var(--accent-cyan); font-weight: 600;">{{ $applicant->preference->visa_category ?? 'B1/B2' }}</span></div>
                        <div><strong>Passport:</strong> <span style="font-family: 'JetBrains Mono', monospace;">{{ $applicant->passport_number }}</span></div>
                        <div><strong>DS-160:</strong> <span style="font-family: 'JetBrains Mono', monospace;">{{ $applicant->ds160_confirmation_number }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Booking Status Highlight -->
            <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-glass); border-radius: 12px; padding: 0.75rem 1.25rem; display: flex; align-items: center; gap: 1rem;">
                <div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Account Profile Mode</div>
                    <div style="font-size: 0.85rem; font-weight: 600; color: #38bdf8;">Managed by Sathwara InfoTech</div>
                </div>
                <div style="width: 1px; height: 28px; background: var(--border-glass);"></div>
                <div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Client Permission</div>
                    <div style="font-size: 0.85rem; font-weight: 600; color: #94a3b8;">View-Only Access</div>
                </div>
            </div>

        </div>

        <!-- Metric Badges Row -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-glass);">
            <div style="background: rgba(0,0,0,0.25); padding: 0.85rem 1rem; border-radius: 10px; border: 1px solid var(--border-glass);">
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Last Portal Scan</div>
                <div style="font-size: 1.05rem; font-weight: 700; margin-top: 0.2rem; color: var(--text-primary);">
                    {{ $task->last_check_at ? $task->last_check_at->diffForHumans() : 'Pending first scan' }}
                </div>
            </div>

            <div style="background: rgba(0,0,0,0.25); padding: 0.85rem 1rem; border-radius: 10px; border: 1px solid var(--border-glass);">
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Next Scheduled Scan</div>
                <div style="font-size: 1.05rem; font-weight: 700; margin-top: 0.2rem; color: var(--accent-cyan);">
                    {{ $task->next_check_at ? $task->next_check_at->format('H:i:s') . ' (' . $task->next_check_at->diffForHumans() . ')' : 'Paused' }}
                </div>
            </div>

            <div style="background: rgba(0,0,0,0.25); padding: 0.85rem 1rem; border-radius: 10px; border: 1px solid var(--border-glass);">
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Total Checks Completed</div>
                <div style="font-size: 1.05rem; font-weight: 700; margin-top: 0.2rem; color: #a855f7;">
                    {{ number_format($task->total_checks_run) }} scans
                </div>
            </div>

            <div style="background: rgba(0,0,0,0.25); padding: 0.85rem 1rem; border-radius: 10px; border: 1px solid var(--border-glass);">
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Monitoring Frequency</div>
                <div style="font-size: 0.95rem; font-weight: 700; margin-top: 0.2rem; color: #34d399;">
                    Every {{ $schedule->interval_minutes ?? 5 }} Minutes
                </div>
            </div>
        </div>
    </div>

    <!-- MFA / OTP Verification Required Modal Banner -->
    @if($task->status === 'awaiting_action' && $pendingChallenge)
        <div class="glass-card" style="border: 2px solid var(--accent-amber); background: linear-gradient(135deg, rgba(245, 158, 11, 0.12) 0%, rgba(15, 23, 42, 0.95) 100%); animation: pulse-border 2s infinite;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.6rem; color: var(--accent-amber); font-weight: 700; font-size: 1.15rem; margin-bottom: 0.35rem;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        Two-Factor Authentication (OTP) Required
                    </div>
                    <p style="color: var(--text-secondary); font-size: 0.92rem; max-width: 650px; line-height: 1.5;">
                        {{ $pendingChallenge['prompt'] ?? 'The USVisaScheduling session encountered an authentication checkpoint. Enter the OTP code sent to your registered contact.' }}
                    </p>
                    <div style="margin-top: 0.6rem; font-size: 0.85rem; color: var(--text-muted);">
                        ⚡ <strong>Test Mode Simulator:</strong> The generated portal test OTP is <code style="background: rgba(245, 158, 11, 0.25); color: #fde68a; padding: 0.2rem 0.6rem; border-radius: 6px; font-weight: 700; font-size: 1rem;">{{ $pendingChallenge['test_otp'] ?? '123456' }}</code>
                    </div>
                </div>

                <form method="POST" action="{{ route('customer.monitoring.otp') }}" style="display: flex; align-items: center; gap: 0.75rem;">
                    @csrf
                    <input type="text" name="otp" value="{{ $pendingChallenge['test_otp'] ?? '' }}" required placeholder="6-digit OTP" class="form-input" style="width: 160px; font-size: 1.1rem; letter-spacing: 2px; text-align: center; font-weight: 700; border-color: var(--accent-amber);">
                    <button type="submit" class="btn btn-warning" id="submit-otp-btn">
                        Verify & Resume
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Appointment Book Dates & Opportunities Section -->
    <div class="glass-card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <div>
                <h3 style="font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; gap: 0.6rem;">
                    <span style="color: var(--accent-emerald);">📅</span> My Visa Appointment Dates & Bookings
                </h3>
                <p style="color: var(--text-secondary); font-size: 0.85rem;">Official appointment dates detected and booked for your profile.</p>
            </div>
            <span style="font-size: 0.82rem; background: rgba(16, 185, 129, 0.15); color: #34d399; padding: 0.3rem 0.75rem; border-radius: 8px; font-weight: 600;">
                {{ $opportunities->count() }} Recorded Slots
            </span>
        </div>

        @if($opportunities->isEmpty())
            <div style="text-align: center; padding: 3rem 1.5rem; background: rgba(0,0,0,0.2); border-radius: 12px; border: 1px dashed var(--border-glass);">
                <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">⏳</div>
                <div style="font-weight: 700; font-size: 1.05rem; margin-bottom: 0.35rem;">Monitoring In Progress - Awaiting Open Slots</div>
                <p style="color: var(--text-muted); font-size: 0.88rem; max-width: 480px; margin: 0 auto;">
                    Sathwara InfoTech's automated server worker is actively scanning official consulates for <strong>{{ $applicant->preference->visa_category ?? 'B1/B2' }}</strong>. Your detected dates and confirmed appointment will appear here as soon as found.
                </p>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.25rem;">
                @foreach($opportunities as $slot)
                    <div style="background: rgba(15, 23, 42, 0.75); border: 2px solid {{ $slot->status === 'confirmed_booked' ? '#10b981' : 'rgba(59, 130, 246, 0.4)' }}; border-radius: 14px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-size: 1.25rem;">🏛️</span>
                                    <strong style="font-size: 1.1rem; color: var(--text-primary);">{{ $slot->consulate_location }} Consulate</strong>
                                </div>
                                @if($slot->status === 'confirmed_booked')
                                    <span class="status-badge badge-active" style="background: rgba(16, 185, 129, 0.2); color: #34d399; font-size: 0.72rem;">✓ Booked & Confirmed</span>
                                @elseif($slot->status === 'available')
                                    <span class="status-badge badge-active" style="font-size: 0.72rem;">Open Now</span>
                                @else
                                    <span class="status-badge badge-paused" style="font-size: 0.72rem;">{{ ucfirst($slot->status) }}</span>
                                @endif
                            </div>

                            <div style="background: rgba(0,0,0,0.3); border-radius: 10px; padding: 0.85rem; margin-bottom: 1rem;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem; font-size: 0.85rem;">
                                    <span style="color: var(--text-muted);">Appointment Date:</span>
                                    <strong style="color: #38bdf8; font-size: 1.05rem;">{{ $slot->appointment_date->format('l, F d, Y') }}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem; font-size: 0.85rem;">
                                    <span style="color: var(--text-muted);">Slot Time:</span>
                                    <strong style="color: var(--text-primary); font-family: 'JetBrains Mono', monospace;">{{ $slot->appointment_time }}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                                    <span style="color: var(--text-muted);">Available Slots:</span>
                                    <span style="color: #34d399; font-weight: 700;">{{ $slot->available_slots }} seats</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                                Slot Detected: {{ $slot->detected_at->diffForHumans() }}
                            </div>
                            @if($slot->status === 'available')
                                <form method="POST" action="{{ route('customer.slots.book', $slot->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-emerald" style="width: 100%;">
                                        Confirm & Book This Date
                                    </button>
                                </form>
                            @else
                                <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; text-align: center; padding: 0.6rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem;">
                                    🎉 Appointment Booked & Locked
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Two-Column Layout: Read-Only Preferences & Live Logs -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.75rem;">
        
        <!-- Read-Only Applicant Profile Card (User CANNOT edit) -->
        <div class="glass-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
                    <span>🔒</span> Configured Visa Preferences
                </h3>
                <span class="status-badge" style="background: rgba(148, 163, 184, 0.15); color: #94a3b8; font-size: 0.7rem;">Read-Only</span>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.82rem; margin-bottom: 1.25rem;">
                Managed exclusively by Sathwara InfoTech. To update, contact your administrator.
            </p>

            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-glass); border-radius: 10px; padding: 0.85rem 1rem;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Applicant Full Name</div>
                    <div style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-top: 0.2rem;">{{ $applicant->full_name }}</div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                    <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-glass); border-radius: 10px; padding: 0.85rem 1rem;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Passport Number</div>
                        <div style="font-size: 0.95rem; font-weight: 700; font-family: 'JetBrains Mono', monospace; color: var(--text-primary); margin-top: 0.2rem;">{{ $applicant->passport_number }}</div>
                    </div>
                    <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-glass); border-radius: 10px; padding: 0.85rem 1rem;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">DS-160 Confirmation</div>
                        <div style="font-size: 0.95rem; font-weight: 700; font-family: 'JetBrains Mono', monospace; color: var(--text-primary); margin-top: 0.2rem;">{{ $applicant->ds160_confirmation_number }}</div>
                    </div>
                </div>

                <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-glass); border-radius: 10px; padding: 0.85rem 1rem;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Visa Classification</div>
                    <div style="font-size: 1rem; font-weight: 700; color: #38bdf8; margin-top: 0.2rem;">{{ $applicant->preference->visa_category ?? 'B1/B2 - Business & Tourism' }}</div>
                </div>

                <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-glass); border-radius: 10px; padding: 0.85rem 1rem;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.4rem;">Target Consulates</div>
                    <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                        @foreach($applicant->preference->preferred_locations ?? ['Mumbai'] as $loc)
                            <span style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 6px; padding: 0.25rem 0.6rem; font-size: 0.8rem; font-weight: 600;">
                                {{ $loc }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                    <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-glass); border-radius: 10px; padding: 0.85rem 1rem;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Earliest Date</div>
                        <div style="font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin-top: 0.2rem;">
                            {{ optional($applicant->preference->earliest_date ?? null)->format('M d, Y') ?? 'Any available' }}
                        </div>
                    </div>
                    <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-glass); border-radius: 10px; padding: 0.85rem 1rem;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Latest Date</div>
                        <div style="font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin-top: 0.2rem;">
                            {{ optional($applicant->preference->latest_date ?? null)->format('M d, Y') ?? 'Any available' }}
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 1.25rem; padding: 0.85rem 1rem; border-radius: 10px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); font-size: 0.82rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.6rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                <span>To modify your passport, DS-160, or appointment window, contact <strong>Sathwara InfoTech Support</strong>.</span>
            </div>
        </div>

        <!-- Live Activity Log & Audit Trail -->
        <div class="glass-card" style="display: flex; flex-direction: column;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
                        <span>📋</span> Activity History & Status Logs
                    </h3>
                    <p style="color: var(--text-secondary); font-size: 0.85rem;">Audit record of server-side monitoring tasks.</p>
                </div>
                <span class="status-badge badge-active" style="font-size: 0.72rem;">Live Feed</span>
            </div>

            <div style="flex: 1; max-height: 480px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.75rem; padding-right: 0.4rem;">
                @forelse($logs as $log)
                    <div style="background: rgba(0,0,0,0.3); border-radius: 10px; padding: 0.85rem 1rem; border-left: 3px solid {{ $log->level === 'success' ? '#10b981' : ($log->level === 'warning' ? '#f59e0b' : ($log->level === 'error' ? '#ef4444' : '#3b82f6')) }};">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.3rem;">
                            <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; font-weight: 700; color: {{ $log->level === 'success' ? '#34d399' : ($log->level === 'warning' ? '#fbbf24' : ($log->level === 'error' ? '#f87171' : '#60a5fa')) }};">
                                [{{ $log->event_type ?? ($log->action ?? 'INFO') }}]
                            </span>
                            <span style="font-size: 0.72rem; color: var(--text-muted);">{{ $log->created_at->format('H:i:s') }} &bull; {{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.4;">
                            {{ $log->message ?? ($log->details ?? '') }}
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-muted); padding: 2rem;">No logs recorded yet.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
