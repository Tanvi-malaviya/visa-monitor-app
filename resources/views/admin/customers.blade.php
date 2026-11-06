@extends('layouts.app')

@section('title', 'Customer & Applicant Directory - Sathwara InfoTech')

@section('styles')
<style>
    .applicants-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        text-align: left;
    }
    .applicants-table thead tr th {
        background: rgba(15, 23, 42, 0.95);
        color: #94a3b8;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.75px;
        padding: 0.9rem 1.15rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        white-space: nowrap;
    }
    .applicants-table tbody tr {
        transition: background 0.15s ease;
    }
    .applicants-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.025);
    }
    .applicants-table tbody tr td {
        padding: 0.95rem 1.15rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .action-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(15, 23, 42, 0.6);
        color: #94a3b8;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        padding: 0;
        flex-shrink: 0;
    }
    .action-icon-btn:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.25);
        transform: translateY(-1px);
    }
    .action-icon-btn.btn-delete:hover {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.35);
    }
    @keyframes modalPopIn {
        from {
            opacity: 0;
            transform: scale(0.94);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Cards Grid View Styles (Compact & Sleek) */
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 0.9rem;
        padding: 1rem;
    }
    .applicant-card {
        background: rgba(15, 23, 42, 0.65);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 11px;
        padding: 0.85rem 0.95rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 0.65rem;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
    }
    .applicant-card:hover {
        border-color: rgba(56, 189, 248, 0.4);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.45), 0 0 16px rgba(56, 189, 248, 0.12);
        transform: translateY(-2px);
    }
    .view-toggle-btn {
        background: transparent;
        border: none;
        color: #94a3b8;
        padding: 0.35rem 0.7rem;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: all 0.2s ease;
    }
    .view-toggle-btn:hover {
        color: #ffffff;
    }
    .view-toggle-btn.active {
        background: #0284c7;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.4);
    }
</style>
@endsection


@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Flash Message Banners -->
    @if(session('success'))
        <div class="glass-card" style="border: 1px solid rgba(16, 185, 129, 0.4); background: rgba(16, 185, 129, 0.12); padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; color: #34d399; font-weight: 600;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <div>{{ session('success') }}</div>
            </div>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #34d399; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="glass-card" style="border: 1px solid rgba(244, 63, 94, 0.4); background: rgba(244, 63, 94, 0.12); padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; color: var(--accent-rose); font-weight: 600;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <div>{{ session('error') }}</div>
            </div>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: var(--accent-rose); font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
    @endif

    <!-- Top Header & Actions -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(59, 130, 246, 0.15); border: 1px solid var(--border-accent); padding: 0.35rem 0.85rem; border-radius: 9999px; margin-bottom: 0.5rem;">
                <span class="pulse-dot"></span>
                <span style="font-size: 0.75rem; font-weight: 700; color: var(--accent-cyan); text-transform: uppercase;">Central Client Administration</span>
            </div>
            <h2 style="font-size: 1.85rem; font-weight: 800; letter-spacing: -0.5px;">Applicant Directory & Auto-Fill Hub</h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                Enrolled clients, third-party portal integration (<code style="color: var(--accent-cyan);">usvisascheduling.com</code>), Robot Login & Sign Up.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <button onclick="document.getElementById('add-customer-modal').style.display='flex'" class="btn btn-primary" id="open-add-customer-modal-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Enroll New Customer & Applicant
            </button>
        </div>
    </div>

    <!-- Applicants & Customers Directory Table -->
    <div class="glass-card" style="padding: 0; overflow: hidden; border-radius: 14px; border: 1px solid var(--border-glass);">
        <div style="padding: 1.15rem 1.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; background: rgba(15, 23, 42, 0.6);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <span>Managed Visa Applicants</span>
                    <span style="font-size: 0.78rem; background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); padding: 0.15rem 0.55rem; border-radius: 9999px;">{{ $customers->total() }} Total</span>
                </h3>
            </div>

            <!-- Right: View Switcher & Instant Search -->
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <!-- View Switcher Toggle -->
                <div style="display: flex; background: rgba(0, 0, 0, 0.4); padding: 0.2rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.08);">
                    <button type="button" id="btn-view-cards" onclick="switchApplicantView('cards')" class="view-toggle-btn active" title="Cards Grid View">
                        <span>🗂️</span>
                        <span>Cards</span>
                    </button>
                    <button type="button" id="btn-view-table" onclick="switchApplicantView('table')" class="view-toggle-btn" title="Table List View">
                        <span>📋</span>
                        <span>Table</span>
                    </button>
                </div>

                <!-- Instant Search Input -->
                <div style="position: relative; width: 260px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none;">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="applicant-search-box" onkeyup="filterApplicants()" placeholder="Search applicants..." class="form-input" style="padding: 0.45rem 0.85rem 0.45rem 2.4rem; font-size: 0.82rem; border-radius: 8px;">
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- CARDS GRID VIEW (MODERN DEFAULT VIEW) -->
        <!-- ================================================================= -->
        <div id="applicants-card-grid" class="cards-grid">
            @forelse($customers as $cust)
                @php
                    $appl = $cust->applicants->first();
                    $pref = $appl ? $appl->preference : null;
                    $task = $appl ? $appl->monitoringTask : null;
                    $bookedSlot = $task ? $task->opportunities->where('status', 'confirmed_booked')->first() : null;

                    $applicantJson = $appl ? json_encode([
                        'id' => $appl->id,
                        'name' => $appl->full_name,
                        'first_name' => $appl->first_name,
                        'last_name' => $appl->last_name,
                        'passport' => $appl->passport_number,
                        'ds160' => $appl->ds160_confirmation_number,
                        'portal_username' => $appl->portal_username ?? ($appl->user->email ?? 'applicant@example.com'),
                        'portal_password' => $appl->portal_password ?? 'VisaSecure@2026',
                        'visa_category' => $pref ? $pref->visa_category : 'B1/B2 - Business & Tourism',
                        'locations' => $pref ? implode(', ', $pref->preferred_locations) : 'Mumbai, New Delhi',
                        'autofill_url' => route('admin.customers.autofill', $appl->id),
                    ]) : null;

                    $editData = $appl ? json_encode([
                        'id' => $appl->id,
                        'name' => $appl->full_name,
                        'portal_username' => $appl->portal_username,
                        'portal_password' => $appl->portal_password,
                        'passport_number' => $appl->passport_number,
                        'ds160' => $appl->ds160_confirmation_number,
                        'visa_category' => $pref ? $pref->visa_category : 'B1/B2 - Business & Tourism',
                        'locations' => $pref && $pref->preferred_locations ? $pref->preferred_locations : ['Mumbai', 'New Delhi'],
                        'earliest_date' => optional($pref?->earliest_date)->format('Y-m-d') ?? '',
                        'latest_date' => optional($pref?->latest_date)->format('Y-m-d') ?? '',
                        'interval' => $task && $task->schedule ? $task->schedule->interval_minutes : 5,
                        'update_url' => route('admin.customers.update', $appl->id),
                    ]) : null;
                @endphp

                <div class="applicant-card" data-applicant-search="{{ strtolower($cust->name . ' ' . ($appl ? $appl->full_name . ' ' . $appl->passport_number . ' ' . $appl->ds160_confirmation_number . ' ' . $appl->portal_username : '') . ' ' . $cust->email) }}">
                    <div>
                        <!-- Card Top: Avatar + Name (Left) & Status Badge (Right) -->
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.35rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0; flex: 1;">
                                <div style="width: 34px; height: 34px; border-radius: 9px; background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.82rem; color: white; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3); flex-shrink: 0;">
                                    {{ strtoupper(substr($cust->name, 0, 2)) }}
                                </div>
                                <div style="min-width: 0; flex: 1;">
                                    <h4 style="font-size: 0.92rem; font-weight: 800; color: #f8fafc; margin: 0; line-height: 1.25; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $appl ? $appl->full_name : $cust->name }}">
                                        {{ $appl ? $appl->full_name : $cust->name }}
                                    </h4>
                                </div>
                            </div>

                            <!-- Live Status Badge (Top Right) -->
                            <div style="flex-shrink: 0;">
                                @if($bookedSlot)
                                    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); border-radius: 6px; padding: 0.18rem 0.42rem; text-align: right;">
                                        <div style="font-size: 0.6rem; text-transform: uppercase; font-weight: 800; color: #34d399; display: flex; align-items: center; gap: 0.2rem; justify-content: flex-end;">
                                            <span>✓</span>
                                            <span>BOOKED</span>
                                        </div>
                                        <div style="font-size: 0.65rem; color: #e2e8f0; font-weight: 600; white-space: nowrap;">
                                            {{ $bookedSlot->consulate_location }} • {{ $bookedSlot->appointment_date->format('M d') }}
                                        </div>
                                    </div>
                                @elseif($task)
                                    @if($task->status === 'active')
                                        <span class="status-badge badge-active" style="font-size: 0.63rem; padding: 0.18rem 0.42rem;"><span class="pulse-dot" style="width: 5px; height: 5px;"></span> Scanning ({{ $task->schedule->interval_minutes ?? 5 }}m)</span>
                                    @elseif($task->status === 'awaiting_action')
                                        <span class="status-badge badge-warning" style="font-size: 0.63rem; padding: 0.18rem 0.42rem;"><span class="pulse-dot" style="width: 5px; height: 5px;"></span> Needs OTP</span>
                                    @elseif($task->status === 'slot_found')
                                        <span class="status-badge badge-success" style="font-size: 0.63rem; padding: 0.18rem 0.42rem;"><span class="pulse-dot" style="width: 5px; height: 5px;"></span> Slot Found</span>
                                    @else
                                        <span class="status-badge badge-paused" style="font-size: 0.63rem; padding: 0.18rem 0.42rem;">Paused</span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Contact Details: Email & Mobile on separate lines with wrap protection -->
                        <div style="margin-bottom: 0.6rem; display: flex; flex-direction: column; gap: 0.2rem;">
                            <!-- Email ID (Wraps cleanly to next line if long) -->
                            <div style="font-size: 0.72rem; color: #cbd5e1; line-height: 1.35; word-break: break-all; display: flex; align-items: flex-start; gap: 0.35rem;">
                                <span style="color: #64748b; font-size: 0.7rem; flex-shrink: 0; margin-top: 1px;">✉️</span>
                                <span title="{{ $cust->email }}">{{ $cust->email }}</span>
                            </div>

                            <!-- Mobile Number (On its own separate line) -->
                            @if($cust->phone)
                                <div style="font-size: 0.71rem; color: #94a3b8; display: flex; align-items: center; gap: 0.35rem;">
                                    <span style="color: #64748b; font-size: 0.7rem; flex-shrink: 0;">📞</span>
                                    <span style="color: #93c5fd; font-family: 'JetBrains Mono', monospace; font-weight: 500;">{{ $cust->phone }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Credentials Box (Compact) -->
                        <div style="background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 8px; padding: 0.55rem 0.75rem; margin-bottom: 0.65rem; display: flex; flex-direction: column; gap: 0.35rem;">
                            @if($appl)
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.4rem; flex-wrap: wrap;">
                                    <div style="display: inline-flex; align-items: center; gap: 0.3rem; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.25); padding: 0.12rem 0.45rem; border-radius: 5px; font-family: 'JetBrains Mono', monospace; font-size: 0.74rem; font-weight: 700; color: #38bdf8;">
                                        <span>🛂</span>
                                        <span>{{ $appl->passport_number }}</span>
                                    </div>

                                    <div style="display: inline-flex; align-items: center; gap: 0.3rem; font-family: 'JetBrains Mono', monospace; font-size: 0.72rem;">
                                        <span style="color: #64748b;">DS-160:</span>
                                        <span style="color: #e2e8f0; font-weight: 600;">{{ $appl->ds160_confirmation_number }}</span>
                                        <button type="button" onclick="copyText('{{ $appl->ds160_confirmation_number }}', '✓ DS-160 copied!')" title="Copy DS-160" style="background: none; border: none; cursor: pointer; color: #64748b; font-size: 0.7rem; padding: 0;" onmouseover="this.style.color='#38bdf8'" onmouseout="this.style.color='#64748b'">📋</button>
                                    </div>
                                </div>

                                <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.04); padding-top: 0.3rem;">
                                    <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.72rem; color: #94a3b8; min-width: 0; flex: 1;">
                                        <span style="width: 5px; height: 5px; border-radius: 50%; background: #10b981; flex-shrink: 0;"></span>
                                        <span style="color: #34d399; font-weight: 700;">Portal:</span>
                                        <span style="font-family: 'JetBrains Mono', monospace; color: #cbd5e1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; min-width: 0; flex: 1;" title="{{ $appl->portal_username ?? $cust->email }}">{{ $appl->portal_username ?? $cust->email }}</span>
                                    </div>
                                    <button type="button" onclick="copyText('{{ $appl->portal_username ?? $cust->email }}', '✓ Portal Username copied!')" title="Copy Username" style="background: none; border: none; cursor: pointer; color: #64748b; font-size: 0.7rem; padding: 0; flex-shrink: 0; margin-left: 0.35rem;" onmouseover="this.style.color='#38bdf8'" onmouseout="this.style.color='#64748b'">📋</button>
                                </div>
                            @else
                                <div style="color: #64748b; font-size: 0.74rem;">No applicant linked</div>
                            @endif
                        </div>

                        <!-- Visa Category & Consulates -->
                        @if($pref)
                            <div style="margin-bottom: 0.25rem;">
                                <div style="font-size: 0.74rem; font-weight: 700; color: #93c5fd; margin-bottom: 0.25rem;">
                                    {{ $pref->visa_category }}
                                </div>
                                <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                    @foreach($pref->preferred_locations as $loc)
                                        <span style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08); padding: 0.08rem 0.38rem; border-radius: 4px; font-size: 0.65rem; color: #cbd5e1;">
                                            {{ $loc }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Bottom Actions Row (Compact) -->
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.4rem; border-top: 1px solid rgba(255, 255, 255, 0.06); padding-top: 0.65rem; margin-top: auto;">
                        @if($appl)
                            <button type="button" 
                                    class="btn btn-emerald btn-sm" 
                                    title="Launch Live Google Chrome & Auto-Login on Official Third-Party Portal"
                                    style="flex: 1; background: linear-gradient(135deg, #059669 0%, #0284c7 100%); font-weight: 700; padding: 0.38rem 0.7rem; border-radius: 7px; box-shadow: 0 3px 10px rgba(5, 150, 105, 0.3); font-size: 0.76rem; display: inline-flex; align-items: center; justify-content: center; gap: 0.3rem; cursor: pointer; border: none;"
                                    data-applicant="{{ $applicantJson }}"
                                    onclick="quickLaunchChrome(this)">
                                <span>🚀</span>
                                <span>Auto-Fill & Login</span>
                            </button>

                            <button type="button" 
                                    onclick='openEditApplicantModal({!! $editData !!})' 
                                    class="action-icon-btn" 
                                    style="width: 29px; height: 29px; border-radius: 7px;"
                                    title="Edit Preferences & Credentials">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                        @endif

                        <button type="button" 
                                class="action-icon-btn btn-delete" 
                                style="width: 29px; height: 29px; border-radius: 7px;"
                                title="Delete Applicant"
                                onclick="confirmDeleteApplicant('{{ $cust->id }}', '{{ addslashes($appl ? $appl->full_name : $cust->name) }}', '{{ route('admin.customers.delete', $cust->id) }}')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>

                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1.5rem; color: var(--text-muted);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">👥</div>
                    <div style="font-size: 1rem; font-weight: 700; color: #94a3b8;">No customers enrolled yet</div>
                    <div style="font-size: 0.85rem; margin-top: 0.35rem;">Click "Enroll New Customer & Applicant" to add.</div>
                </div>
            @endforelse
        </div>

        <!-- ================================================================= -->
        <!-- TABLE VIEW (ALTERNATIVE LIST VIEW) -->
        <!-- ================================================================= -->
        <div id="applicants-table-container" style="display: none; overflow-x: auto;">
            <table class="applicants-table">
                <thead>
                    <tr>
                        <th style="min-width: 220px;">Applicant & Contact</th>
                        <th style="min-width: 170px;">Passport & DS-160</th>
                        <th style="min-width: 190px;">Third-Party Portal Login</th>
                        <th style="min-width: 180px;">Preferences & Consulates</th>
                        <th style="min-width: 170px;">Live Status / Booked Date</th>
                        <th style="text-align: right; min-width: 220px;">Robot Automation</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $cust)
                        @php
                            $appl = $cust->applicants->first();
                            $pref = $appl ? $appl->preference : null;
                            $task = $appl ? $appl->monitoringTask : null;
                            $bookedSlot = $task ? $task->opportunities->where('status', 'confirmed_booked')->first() : null;
                        @endphp
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.88rem; color: white; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3); flex-shrink: 0;">
                                        {{ strtoupper(substr($cust->name, 0, 2)) }}
                                    </div>
                                    <div style="min-width: 0;">
                                        <div style="font-weight: 700; color: #f8fafc; font-size: 0.92rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $appl ? $appl->full_name : $cust->name }}
                                        </div>
                                        <div style="font-size: 0.76rem; color: #94a3b8; margin-top: 0.15rem; display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                                            <span>{{ $cust->email }}</span>
                                            @if($cust->phone)
                                                <span style="color: #64748b;">•</span>
                                                <span style="color: #94a3b8;">{{ $cust->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if($appl)
                                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                        <div style="display: inline-flex; align-items: center; gap: 0.35rem; width: fit-content; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.25); padding: 0.2rem 0.55rem; border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; font-weight: 700; color: #38bdf8;">
                                            <span>🛂</span>
                                            <span>{{ $appl->passport_number }}</span>
                                        </div>
                                        <div style="font-size: 0.74rem; font-family: 'JetBrains Mono', monospace; color: #94a3b8; display: flex; align-items: center; gap: 0.35rem;">
                                            <span style="color: #64748b;">DS-160:</span>
                                            <span style="color: #e2e8f0; font-weight: 600;">{{ $appl->ds160_confirmation_number }}</span>
                                            <button type="button" onclick="copyText('{{ $appl->ds160_confirmation_number }}', '✓ DS-160 copied!')" title="Copy DS-160" style="background: none; border: none; cursor: pointer; color: #64748b; padding: 0; font-size: 0.7rem;" onmouseover="this.style.color='#38bdf8'" onmouseout="this.style.color='#64748b'">📋</button>
                                        </div>
                                    </div>
                                @else
                                    <span style="color: #64748b; font-size: 0.8rem;">No applicant linked</span>
                                @endif
                            </td>

                            <td>
                                @if($appl && $appl->portal_username)
                                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                        <div style="display: inline-flex; align-items: center; gap: 0.35rem; width: fit-content; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.25); padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700; color: #34d399;">
                                            <span style="width: 5px; height: 5px; border-radius: 50%; background: #10b981;"></span>
                                            <span>AtlasAuth Linked</span>
                                        </div>
                                        <div style="font-size: 0.78rem; font-family: 'JetBrains Mono', monospace; color: #cbd5e1; display: flex; align-items: center; gap: 0.35rem; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <span title="{{ $appl->portal_username }}">{{ $appl->portal_username }}</span>
                                            <button type="button" onclick="copyText('{{ $appl->portal_username }}', '✓ Portal username copied!')" title="Copy username" style="background: none; border: none; cursor: pointer; color: #64748b; padding: 0; font-size: 0.7rem;" onmouseover="this.style.color='#38bdf8'" onmouseout="this.style.color='#64748b'">📋</button>
                                        </div>
                                    </div>
                                @else
                                    <span class="status-badge badge-paused" style="font-size: 0.7rem;">Not Configured</span>
                                @endif
                            </td>

                            <td>
                                @if($pref)
                                    <div style="font-size: 0.82rem; font-weight: 700; color: #93c5fd; margin-bottom: 0.3rem;">
                                        {{ $pref->visa_category }}
                                    </div>
                                    <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                        @foreach($pref->preferred_locations as $loc)
                                            <span style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08); padding: 0.12rem 0.45rem; border-radius: 4px; font-size: 0.7rem; color: #cbd5e1;">
                                                {{ $loc }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <td>
                                @if($bookedSlot)
                                    <div style="display: inline-flex; flex-direction: column; gap: 0.2rem; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; padding: 0.4rem 0.7rem;">
                                        <div style="font-size: 0.7rem; text-transform: uppercase; font-weight: 800; color: #34d399; display: flex; align-items: center; gap: 0.3rem;">
                                            <span>✓</span>
                                            <span>Booked • {{ $bookedSlot->consulate_location }}</span>
                                        </div>
                                        <div style="font-size: 0.75rem; color: #e2e8f0; font-weight: 600;">
                                            {{ $bookedSlot->appointment_date->format('M d, Y') }} ({{ $bookedSlot->appointment_time }})
                                        </div>
                                    </div>
                                @elseif($task)
                                    @if($task->status === 'active')
                                        <span class="status-badge badge-active" style="font-size: 0.74rem;"><span class="pulse-dot"></span> Scanning ({{ $task->schedule->interval_minutes ?? 5 }}m)</span>
                                    @elseif($task->status === 'awaiting_action')
                                        <span class="status-badge badge-warning" style="font-size: 0.74rem;"><span class="pulse-dot"></span> Needs OTP</span>
                                    @elseif($task->status === 'slot_found')
                                        <span class="status-badge badge-success" style="font-size: 0.74rem;"><span class="pulse-dot"></span> Slot Available</span>
                                    @else
                                        <span class="status-badge badge-paused" style="font-size: 0.74rem;">Paused</span>
                                    @endif
                                @endif
                            </td>

                            <td style="text-align: right; white-space: nowrap;">
                                <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 0.4rem;">
                                    @if($appl)
                                        @php
                                            $applicantJson = json_encode([
                                                'id' => $appl->id,
                                                'name' => $appl->full_name,
                                                'first_name' => $appl->first_name,
                                                'last_name' => $appl->last_name,
                                                'passport' => $appl->passport_number,
                                                'ds160' => $appl->ds160_confirmation_number,
                                                'portal_username' => $appl->portal_username ?? ($appl->user->email ?? 'tanvi.malaviya@example.com'),
                                                'portal_password' => $appl->portal_password ?? 'TanviSecure@2026',
                                                'visa_category' => $pref ? $pref->visa_category : 'B1/B2 - Business & Tourism',
                                                'locations' => $pref ? implode(', ', $pref->preferred_locations) : 'Mumbai, New Delhi',
                                                'autofill_url' => route('admin.customers.autofill', $appl->id),
                                            ]);
                                        @endphp
                                        <!-- Primary 1-Click Chrome Auto-Fill & Launch Button -->
                                        <button type="button" 
                                                class="btn btn-emerald btn-sm" 
                                                title="Launch Live Google Chrome & Auto-Login on Official Third-Party Portal"
                                                style="background: linear-gradient(135deg, #059669 0%, #0284c7 100%); font-weight: 700; padding: 0.42rem 0.8rem; border-radius: 8px; box-shadow: 0 3px 12px rgba(5, 150, 105, 0.35); font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.35rem; cursor: pointer;"
                                                data-applicant="{{ $applicantJson }}"
                                                onclick="quickLaunchChrome(this)">
                                            <span>🚀</span>
                                            <span>Auto-Fill & Login</span>
                                        </button>


                                        @php
                                            $editData = json_encode([
                                                'id' => $appl->id,
                                                'name' => $appl->full_name,
                                                'portal_username' => $appl->portal_username,
                                                'portal_password' => $appl->portal_password,
                                                'passport_number' => $appl->passport_number,
                                                'ds160' => $appl->ds160_confirmation_number,
                                                'visa_category' => $pref ? $pref->visa_category : 'B1/B2 - Business & Tourism',
                                                'locations' => $pref && $pref->preferred_locations ? $pref->preferred_locations : ['Mumbai', 'New Delhi'],
                                                'earliest_date' => optional($pref?->earliest_date)->format('Y-m-d') ?? '',
                                                'latest_date' => optional($pref?->latest_date)->format('Y-m-d') ?? '',
                                                'interval' => $task && $task->schedule ? $task->schedule->interval_minutes : 5,
                                                'update_url' => route('admin.customers.update', $appl->id),
                                            ]);
                                        @endphp
                                        <!-- Edit Profile Button -->
                                        <button type="button" 
                                                onclick='openEditApplicantModal({!! $editData !!})' 
                                                class="action-icon-btn" 
                                                title="Edit Preferences & Credentials">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </button>
                                    @endif

                                    <!-- Delete Button (Triggers Pop-up Modal) -->
                                    <button type="button" 
                                            class="action-icon-btn btn-delete" 
                                            title="Delete Applicant"
                                            onclick="confirmDeleteApplicant('{{ $cust->id }}', '{{ addslashes($appl ? $appl->full_name : $cust->name) }}', '{{ route('admin.customers.delete', $cust->id) }}')">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                No customers enrolled yet. Click "Enroll New Customer & Applicant" to add.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-glass);">
            {{ $customers->links() }}
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- DELETE CONFIRMATION POPUP MODAL -->
<!-- ========================================================================= -->
<div id="delete-confirm-modal" style="display: none; align-items: center; justify-content: center; position: fixed; inset: 0; background: rgba(3, 7, 18, 0.85); backdrop-filter: blur(8px); z-index: 10005; padding: 1.5rem;">
    <div class="glass-card" style="width: 100%; max-width: 440px; background: #0c1322; border: 1.5px solid rgba(239, 68, 68, 0.35); box-shadow: 0 25px 60px rgba(0, 0, 0, 0.8), 0 0 40px rgba(239, 68, 68, 0.2); text-align: center; padding: 2rem 1.75rem; border-radius: 16px; animation: modalPopIn 0.2s cubic-bezier(0.16, 1, 0.3, 1); position: relative;">
        
        <!-- Danger Warning Icon -->
        <div style="width: 58px; height: 58px; border-radius: 50%; background: rgba(239, 68, 68, 0.12); border: 1.5px solid rgba(239, 68, 68, 0.35); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.2rem; color: #ef4444;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>

        <h3 style="font-size: 1.25rem; font-weight: 800; color: #f8fafc; margin-bottom: 0.5rem; letter-spacing: -0.3px;">
            Delete Applicant?
        </h3>
        <p style="color: #94a3b8; font-size: 0.88rem; line-height: 1.5; margin-bottom: 1.5rem;">
            Are you sure you want to permanently delete <strong id="delete-applicant-name" style="color: #fca5a5;">Applicant</strong>? This will remove all their appointment monitoring tasks and portal records.
        </p>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 0.75rem; justify-content: center;">
            <button type="button" 
                    onclick="closeDeleteModal()" 
                    class="btn btn-outline" 
                    style="flex: 1; padding: 0.65rem 1rem; font-size: 0.85rem; font-weight: 600; border-radius: 10px; cursor: pointer;">
                Cancel
            </button>

            <form id="delete-modal-form" method="POST" action="" style="flex: 1; margin: 0;">
                @csrf
                <button type="submit" 
                        class="btn btn-danger" 
                        style="width: 100%; padding: 0.65rem 1rem; font-size: 0.85rem; font-weight: 700; border-radius: 10px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none; color: white; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4); cursor: pointer;">
                    Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT APPLICANT POPUP MODAL (COMPACT & SCROLL-FREE) -->
<!-- ========================================================================= -->
<div id="edit-applicant-modal" style="display: none; align-items: center; justify-content: center; position: fixed; inset: 0; background: rgba(3, 7, 18, 0.82); backdrop-filter: blur(8px); z-index: 10002; padding: 1rem;">
    <div class="glass-card" style="width: 100%; max-width: 740px; background: #0c1322; border: 1.5px solid rgba(56, 189, 248, 0.35); border-radius: 14px; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.85), 0 0 35px rgba(56, 189, 248, 0.15); animation: modalPopIn 0.2s cubic-bezier(0.16, 1, 0.3, 1); display: flex; flex-direction: column; overflow: visible; padding: 0;">
        
        <!-- Modal Header -->
        <div style="padding: 0.8rem 1.25rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08); display: flex; align-items: center; justify-content: space-between; background: rgba(15, 23, 42, 0.7); border-radius: 14px 14px 0 0;">
            <div style="display: flex; align-items: center; gap: 0.55rem;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.3); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 1rem;">
                    ✏️
                </div>
                <div>
                    <h3 id="edit-modal-title" style="font-size: 1.05rem; font-weight: 800; color: #f8fafc; margin: 0; letter-spacing: -0.3px;">
                        Edit Applicant
                    </h3>
                    <p style="font-size: 0.74rem; color: #94a3b8; margin: 0;">
                        Update portal credentials & consular preferences
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; width: 30px; height: 30px; border-radius: 8px; font-size: 1.2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                &times;
            </button>
        </div>

        <!-- Compact Form Body (Fits completely without scrolling) -->
        <div style="padding: 1rem 1.25rem; overflow: visible;">
            <form id="edit-applicant-form" method="POST" action="">
                @csrf
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: start;">
                    
                    <!-- LEFT COLUMN -->
                    <div>
                        <!-- Portal Credentials Box -->
                        <div style="background: rgba(14, 165, 233, 0.08); border: 1px solid rgba(14, 165, 233, 0.25); border-radius: 8px; padding: 0.65rem 0.85rem; margin-bottom: 0.65rem;">
                            <div style="font-size: 0.72rem; font-weight: 700; color: #38bdf8; text-transform: uppercase; margin-bottom: 0.4rem; letter-spacing: 0.5px;">
                                🌐 USVisaScheduling Login
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                <div>
                                    <label class="form-label" style="font-size: 0.74rem; margin-bottom: 0.2rem;">Portal Username</label>
                                    <input type="text" id="edit_portal_username" name="portal_username" class="form-input" style="height: 32px; font-size: 0.8rem; padding: 0.25rem 0.6rem;">
                                </div>
                                <div>
                                    <label class="form-label" style="font-size: 0.74rem; margin-bottom: 0.2rem;">Portal Password</label>
                                    <input type="password" id="edit_portal_password" name="portal_password" class="form-input" style="height: 32px; font-size: 0.8rem; padding: 0.25rem 0.6rem;">
                                </div>
                            </div>
                        </div>

                        <!-- Passport & DS-160 Row -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.65rem;">
                            <div>
                                <label class="form-label" style="font-size: 0.74rem; margin-bottom: 0.2rem;">Passport Number</label>
                                <input type="text" id="edit_passport_number" name="passport_number" required class="form-input" style="height: 32px; font-size: 0.8rem; padding: 0.25rem 0.6rem; font-family: 'JetBrains Mono', monospace; text-transform: uppercase;">
                            </div>
                            <div>
                                <label class="form-label" style="font-size: 0.74rem; margin-bottom: 0.2rem;">DS-160 Number</label>
                                <input type="text" id="edit_ds160" name="ds160_confirmation_number" required class="form-input" style="height: 32px; font-size: 0.8rem; padding: 0.25rem 0.6rem; font-family: 'JetBrains Mono', monospace; text-transform: uppercase;">
                            </div>
                        </div>

                        <!-- Visa Category -->
                        <div>
                            <label class="form-label" style="font-size: 0.74rem; margin-bottom: 0.2rem;">Visa Category</label>
                            <select id="edit_visa_category" name="visa_category" class="form-select" style="height: 32px; font-size: 0.8rem; padding: 0.25rem 0.6rem;">
                                @foreach(['B1/B2 - Business & Tourism', 'F-1 - Student', 'H-1B - Specialty Worker', 'L-1 - Intracompany Transferee', 'J-1 - Exchange Visitor'] as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN -->
                    <div>
                        <!-- Target Consulates -->
                        <div style="margin-bottom: 0.65rem;">
                            <label class="form-label" style="font-size: 0.74rem; margin-bottom: 0.25rem;">Target Consulates</label>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.35rem;">
                                @foreach(['Mumbai', 'New Delhi', 'Hyderabad', 'Chennai', 'Kolkata'] as $loc)
                                    <label style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.76rem; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); padding: 0.3rem 0.45rem; border-radius: 6px; cursor: pointer;">
                                        <input type="checkbox" name="preferred_locations[]" value="{{ $loc }}" class="edit-location-cb">
                                        <span>{{ $loc }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Earliest & Latest Date -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.65rem;">
                            <div>
                                <label class="form-label" style="font-size: 0.74rem; margin-bottom: 0.2rem;">Earliest Date</label>
                                <input type="date" id="edit_earliest_date" name="earliest_date" class="form-input" style="height: 32px; font-size: 0.8rem; padding: 0.25rem 0.6rem;">
                            </div>
                            <div>
                                <label class="form-label" style="font-size: 0.74rem; margin-bottom: 0.2rem;">Latest Date</label>
                                <input type="date" id="edit_latest_date" name="latest_date" class="form-input" style="height: 32px; font-size: 0.8rem; padding: 0.25rem 0.6rem;">
                            </div>
                        </div>

                        <!-- Scan Frequency -->
                        <div>
                            <label class="form-label" style="font-size: 0.74rem; margin-bottom: 0.2rem;">Scan Frequency</label>
                            <select id="edit_interval_minutes" name="interval_minutes" class="form-select" style="height: 32px; font-size: 0.8rem; padding: 0.25rem 0.6rem;">
                                @foreach([3, 5, 10, 15, 30] as $m)
                                    <option value="{{ $m }}">Every {{ $m }} Minutes</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>

                <!-- Footer Buttons -->
                <div style="display: flex; justify-content: flex-end; gap: 0.65rem; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 0.75rem; margin-top: 0.85rem;">
                    <button type="button" onclick="closeEditModal()" class="btn btn-outline btn-sm" style="padding: 0.4rem 0.85rem; font-size: 0.8rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.4rem 1.15rem; font-size: 0.8rem; font-weight: 700;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- DUAL MODE ROBOT WORKING MODAL (LOGIN & SIGNUP ON THIRD-PARTY LINK) -->
<!-- ========================================================================= -->
<div id="robot-autofill-modal" style="display: none; align-items: center; justify-content: center; position: fixed; inset: 0; background: rgba(0,0,0,0.88); z-index: 10000; padding: 1.5rem; overflow-y: auto;">
    <div class="glass-card" style="width: 100%; max-width: 1150px; background: #070d19; border: 2px solid #0284c7; box-shadow: 0 0 80px rgba(2, 132, 199, 0.4); max-height: 95vh; overflow-y: auto; padding: 1.5rem; position: relative;">
        
        <!-- Top Header & Controls -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; border-bottom: 1px solid var(--border-glass); padding-bottom: 0.85rem; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.85rem;">
                <div style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7 0%, #10b981 100%); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; box-shadow: 0 0 25px rgba(16, 185, 129, 0.4);">
                    🌐
                </div>
                <div>
                    <h3 style="font-size: 1.35rem; font-weight: 800; letter-spacing: -0.3px;" id="robot-modal-title">Live Third-Party Portal</h3>
                    <div style="font-size: 0.8rem; color: #38bdf8; display: flex; align-items: center; gap: 0.5rem;">
                        <span class="pulse-dot"></span>
                        <span id="active-applicant-badge">Applicant: Tanvi Malaviya (P98765432)</span>
                    </div>
                </div>
            </div>

            <!-- Mode Selector: Login vs Sign Up -->
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="display: flex; background: rgba(15, 23, 42, 0.9); padding: 0.3rem; border-radius: 8px; border: 1px solid var(--border-glass);">
                    <button type="button" 
                            id="tab-mode-login" 
                            onclick="switchRobotMode('login')" 
                            style="background: #0284c7; color: white; border: none; padding: 0.45rem 1rem; border-radius: 6px; font-weight: 700; font-size: 0.82rem; cursor: pointer; transition: all 0.3s ease;">
                        🔑 Mode 1: Sign In & Book
                    </button>
                    <button type="button" 
                            id="tab-mode-signup" 
                            onclick="switchRobotMode('signup')" 
                            style="background: transparent; color: #94a3b8; border: none; padding: 0.45rem 1rem; border-radius: 6px; font-weight: 700; font-size: 0.82rem; cursor: pointer; transition: all 0.3s ease;">
                        📝 Mode 2: Sign Up (New Account)
                    </button>
                </div>
                
                <button type="button" onclick="closeRobotModal()" style="background: rgba(255,255,255,0.08); border: 1px solid var(--border-glass); color: #cbd5e1; width: 36px; height: 36px; border-radius: 8px; font-size: 1.4rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
            </div>
        </div>

        <!-- Live Applicant Credentials Strip -->
        <div style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.85) 100%); border: 1.5px solid rgba(56, 189, 248, 0.35); border-radius: 10px; padding: 0.85rem 1.25rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.85rem; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
            <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                <div>
                    <div style="font-size: 0.68rem; text-transform: uppercase; color: #94a3b8; font-weight: 700; letter-spacing: 0.5px;">Portal Username / Email</div>
                    <div style="display: flex; align-items: center; gap: 0.45rem; margin-top: 0.2rem;">
                        <span id="card-applicant-username" style="font-family: 'JetBrains Mono', monospace; font-size: 0.9rem; color: #38bdf8; font-weight: 700;">tanvi.malaviya@example.com</span>
                        <button type="button" onclick="copyApplicantUsername()" class="btn-copy-tag" title="Copy Username to Clipboard">📋 Copy</button>
                    </div>
                </div>
                
                <div style="border-left: 1px solid rgba(255,255,255,0.1); padding-left: 1.5rem;">
                    <div style="font-size: 0.68rem; text-transform: uppercase; color: #94a3b8; font-weight: 700; letter-spacing: 0.5px;">Portal Password</div>
                    <div style="display: flex; align-items: center; gap: 0.45rem; margin-top: 0.2rem;">
                        <span id="card-applicant-password" style="font-family: 'JetBrains Mono', monospace; font-size: 0.9rem; color: #a7f3d0; font-weight: 700;">••••••••</span>
                        <button type="button" onclick="togglePasswordVisibility()" class="btn-copy-tag" id="btn-toggle-pwd">👁️ Show</button>
                        <button type="button" onclick="copyApplicantPassword()" class="btn-copy-tag" title="Copy Password to Clipboard">📋 Copy</button>
                    </div>
                </div>

                <div style="border-left: 1px solid rgba(255,255,255,0.1); padding-left: 1.5rem;">
                    <div style="font-size: 0.68rem; text-transform: uppercase; color: #94a3b8; font-weight: 700; letter-spacing: 0.5px;">Passport / DS-160</div>
                    <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.2rem; font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #e2e8f0;">
                        <span id="card-applicant-passport">P98765432</span>
                        <span style="color: #64748b;">•</span>
                        <span id="card-applicant-ds160">AA00987654</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Robot Live Status Bar -->
        <div id="portal-live-status-bar" style="display: none; background: rgba(2, 132, 199, 0.18); border: 1.5px solid #0284c7; border-radius: 10px; padding: 0.85rem 1.25rem; margin-bottom: 1rem; align-items: center; justify-content: space-between; box-shadow: 0 4px 15px rgba(2, 132, 199, 0.25);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span class="pulse-dot"></span>
                <div>
                    <div style="font-weight: 800; font-size: 0.92rem; color: #38bdf8;" id="robot-status-title">
                        🤖 Launching Genuine Google Chrome...
                    </div>
                    <div style="font-size: 0.82rem; color: #94a3b8;" id="robot-status-desc">
                        Opening official third-party link. Credentials will be auto-filled and submitted!
                    </div>
                </div>
            </div>
            <div id="robot-status-badge" style="font-size: 0.75rem; font-family: 'JetBrains Mono', monospace; background: rgba(56, 189, 248, 0.2); color: #7dd3fc; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 700;">
                STARTING
            </div>
        </div>

        <!-- ONE-CLICK PRIMARY ACTION: OPEN OFFICIAL PORTAL & AUTO-FILL -->
        <div style="margin-bottom: 1rem;">
            <button type="button" 
                    id="btn-launch-live-chrome" 
                    onclick="launchLiveChromeAutomation()" 
                    class="btn btn-primary" 
                    style="width: 100%; background: linear-gradient(135deg, #10b981 0%, #0284c7 100%); font-weight: 800; padding: 0.85rem 1.4rem; font-size: 1.05rem; box-shadow: 0 0 30px rgba(16, 185, 129, 0.45); display: flex; align-items: center; justify-content: center; gap: 0.6rem; cursor: pointer; border: none; border-radius: 10px;">
                <span style="font-size: 1.3rem;">🚀</span>
                <span id="btn-launch-text">Launch Official Portal & Auto-Login</span>
            </button>
        </div>

        <!-- 100% AUTOMATIC EXTENSION ACTIVATION BOX (5-SECOND SETUP) -->
        <div style="background: rgba(16, 185, 129, 0.08); border: 1.5px solid rgba(16, 185, 129, 0.35); border-radius: 12px; padding: 1.15rem 1.35rem; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem; flex-wrap: wrap; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 800; color: #34d399; font-size: 0.95rem;">
                    <span>🧩</span>
                    <span>100% Automatic Auto-Fill Setup (One-Time 5 Seconds)</span>
                </div>
                <button type="button" onclick="copyExtensionPath()" class="btn-copy-tag" style="background: rgba(16, 185, 129, 0.25); border-color: #34d399; color: #a7f3d0; font-size: 0.78rem; padding: 0.35rem 0.75rem;">
                    📋 Copy Extension Folder Path
                </button>
            </div>
            <p style="font-size: 0.84rem; color: #cbd5e1; line-height: 1.5; margin-bottom: 0.5rem;">
                ઓફિશિયલ પોર્ટલ ખૂલ્યા પછી યુઝરનેમ અને પાસવર્ડ <strong>ઓટોમેટિક ટાઈપ થઈને Sign In સબમિટ થાય</strong> તે માટે તમારા ક્રોમમાં આ એક્સટેન્શન ફક્ત 1 વાર લોડ કરો:
            </p>
            <ol style="font-size: 0.82rem; color: #94a3b8; margin-left: 1.35rem; line-height: 1.7; margin-bottom: 0.4rem;">
                <li>નવા ટેબમાં જાઓ: <code style="color: #38bdf8; background: rgba(0,0,0,0.4); padding: 2px 6px; border-radius: 4px;">chrome://extensions</code> અને ઉપર જમણી બાજુ <strong>Developer mode</strong> ચાલુ કરો.</li>
                <li><strong>"Load unpacked"</strong> બટન પર ક્લિક કરો અને આ પાથ સિલેક્ટ કરો: <code style="color: #34d399; background: rgba(0,0,0,0.4); padding: 2px 6px; border-radius: 4px;">D:\sathwara_infotech\visa-monitor-app\chrome-extension</code></li>
            </ol>
            <div style="font-size: 0.78rem; color: #34d399; font-weight: 600;">
                ✓ આ 1 વાર કરી લેશો એટલે દર વખતે બટન ક્લિક કરતાની સાથે જ આપમેળે લોગિન થઈ જશે!
            </div>
        </div>

        <!-- Official Third-Party Link URL Bar -->
        <div style="background: rgba(15, 23, 42, 0.9); border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 10px; padding: 0.75rem 1.25rem; margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.6rem; flex: 1; min-width: 320px;">
                <span style="font-size: 0.78rem; text-transform: uppercase; font-weight: 700; color: #38bdf8; white-space: nowrap;">
                    🌐 Official Third-Party URL:
                </span>
                <input type="text" 
                       id="thirdparty-custom-url" 
                       value="https://atlasauth.b2clogin.com/atlasauth.onmicrosoft.com/B2C_1A_atoproduction_Atlas_SUSI/oauth2/v2.0/authorize?client_id=607d08d6-b63b-4735-ad82-05dfcff7efa4&nonce=defaultNonce&redirect_uri=https://www.usvisascheduling.com/en-US/SignIn&scope=openid&response_type=id_token&prompt=login&ui_locales=en-US" 
                       class="form-input" 
                       style="font-size: 0.78rem; font-family: 'JetBrains Mono', monospace; padding: 0.4rem 0.75rem; height: 34px; background: rgba(0,0,0,0.5); color: #e2e8f0; border-color: rgba(56, 189, 248, 0.4);"
                       readonly>
            </div>
            <button type="button" onclick="copyPortalUrl()" class="btn btn-outline btn-sm" style="font-size: 0.78rem; padding: 0.4rem 0.85rem; border-color: rgba(255,255,255,0.2);">
                📋 Copy Link
            </button>
        </div>
    </div>
</div>

<div id="add-customer-modal" style="display: none; align-items: center; justify-content: center; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 9999; padding: 1.5rem; overflow-y: auto;">
    <div class="glass-card" style="width: 100%; max-width: 780px; background: #0f172a; border: 1px solid var(--border-glass); max-height: 90vh; overflow-y: auto;">
        
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
            <h3 style="font-size: 1.35rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
                <span>👤</span> Enroll Customer & Auto-Fill Profile
            </h3>
            <button type="button" onclick="document.getElementById('add-customer-modal').style.display='none'" style="background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <p style="color: var(--text-secondary); font-size: 0.88rem; margin-bottom: 1.5rem;">
            Create client account with their official <code style="color: var(--accent-cyan);">usvisascheduling.com</code> credentials for automated booking.
        </p>

        <form method="POST" action="{{ route('admin.customers.store') }}">
            @csrf

            <!-- Section 1: Portal Login Information (From User Photo) -->
            <div style="background: rgba(14, 165, 233, 0.08); border: 1px solid rgba(14, 165, 233, 0.25); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 700; color: #38bdf8; text-transform: uppercase; margin-bottom: 0.25rem;">
                    <span>🌐</span> USVisaScheduling (usvisascheduling.com) Login Credentials
                </div>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">
                    The exact credentials from the official login screen in your photo. Our Playwright engine will use these to automatically log in and fill appointment bookings.
                </p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" for="new_portal_username">Username / Email (Official Portal) *</label>
                        <input type="text" id="new_portal_username" name="portal_username" required class="form-input" placeholder="e.g. user@gmail.com">
                    </div>
                    <div>
                        <label class="form-label" for="new_portal_password">Password (Official Portal) *</label>
                        <input type="password" id="new_portal_password" name="portal_password" required class="form-input" placeholder="••••••••">
                    </div>
                </div>

                <div style="font-size: 0.78rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.5rem; text-transform: uppercase;">
                    Security Checkpoint Answers (3 Security Questions)
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label class="form-label" style="font-size: 0.75rem;">Q1: Mother's Maiden Name</label>
                        <input type="text" name="security_a1" value="Patel" class="form-input" style="font-size: 0.85rem;" placeholder="Answer 1">
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.75rem;">Q2: First Pet Name</label>
                        <input type="text" name="security_a2" value="Rocky" class="form-input" style="font-size: 0.85rem;" placeholder="Answer 2">
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.75rem;">Q3: First School Name</label>
                        <input type="text" name="security_a3" value="St Xaviers" class="form-input" style="font-size: 0.85rem;" placeholder="Answer 3">
                    </div>
                </div>
            </div>

            <!-- Section 2: Personal & Contact Information -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label" for="new_name">Applicant Full Name *</label>
                    <input type="text" id="new_name" name="name" required class="form-input" placeholder="e.g. Ramesh Patel">
                </div>
                <div>
                    <label class="form-label" for="new_email">Client Email (For Portal Notifications) *</label>
                    <input type="email" id="new_email" name="email" required class="form-input" placeholder="ramesh@example.com">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label" for="new_phone">WhatsApp / Contact Phone *</label>
                    <input type="text" id="new_phone" name="phone" required class="form-input" placeholder="+91 98765 43210">
                </div>
                <div>
                    <label class="form-label" for="new_password">Local Portal Password (For Client View-Only Login) *</label>
                    <input type="password" id="new_password" name="password" value="password123" required class="form-input">
                </div>
            </div>

            <!-- Section 3: Passport & DS-160 Details -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label" for="new_passport">Passport Number *</label>
                    <input type="text" id="new_passport" name="passport_number" required class="form-input" placeholder="e.g. Z1234567" style="font-family: 'JetBrains Mono', monospace; text-transform: uppercase;">
                </div>
                <div>
                    <label class="form-label" for="new_ds160">DS-160 Confirmation Number *</label>
                    <input type="text" id="new_ds160" name="ds160_confirmation_number" required class="form-input" placeholder="e.g. AA00A1B2C3" style="font-family: 'JetBrains Mono', monospace; text-transform: uppercase;">
                </div>
            </div>

            <!-- Section 4: Target Consulates & Dates -->
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="new_visa_category">Visa Classification *</label>
                <select id="new_visa_category" name="visa_category" required class="form-select">
                    <option value="B1/B2 - Business & Tourism">B1/B2 - Business & Tourism</option>
                    <option value="F-1 - Student">F-1 - Student</option>
                    <option value="H-1B - Specialty Worker">H-1B - Specialty Worker</option>
                    <option value="L-1 - Intracompany Transferee">L-1 - Intracompany Transferee</option>
                    <option value="J-1 - Exchange Visitor">J-1 - Exchange Visitor</option>
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Preferred Consulates (Select all acceptable) *</label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; margin-top: 0.4rem;">
                    @foreach(['Mumbai', 'New Delhi', 'Hyderabad', 'Chennai', 'Kolkata'] as $loc)
                        <label style="display: flex; align-items: center; gap: 0.45rem; background: rgba(0,0,0,0.3); padding: 0.45rem 0.65rem; border-radius: 8px; border: 1px solid var(--border-glass); font-size: 0.82rem; cursor: pointer;">
                            <input type="checkbox" name="preferred_locations[]" value="{{ $loc }}" checked style="accent-color: var(--accent-blue);">
                            {{ $loc }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label" for="new_earliest">Earliest Acceptable Date</label>
                    <input type="date" id="new_earliest" name="earliest_date" value="{{ now()->addDays(15)->format('Y-m-d') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label" for="new_latest">Latest Acceptable Date</label>
                    <input type="date" id="new_latest" name="latest_date" value="{{ now()->addDays(90)->format('Y-m-d') }}" class="form-input">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label" for="new_interval">Monitoring Scan Frequency *</label>
                <select id="new_interval" name="interval_minutes" class="form-select">
                    <option value="3">Every 3 Minutes (High Priority)</option>
                    <option value="5" selected>Every 5 Minutes (Standard Recommended)</option>
                    <option value="10">Every 10 Minutes</option>
                    <option value="15">Every 15 Minutes</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--border-glass); padding-top: 1.25rem;">
                <button type="button" onclick="document.getElementById('add-customer-modal').style.display='none'" class="btn btn-outline btn-sm">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary" id="save-new-customer-btn">
                    Create & Enable Auto-Fill Engine
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT: DUAL-MODE ROBOT ENGINE (LOGIN & SIGN UP ON THIRD PARTY LINK) -->
<!-- ========================================================================= -->
<style>
.btn-copy-tag {
    background: rgba(56, 189, 248, 0.15);
    border: 1px solid rgba(56, 189, 248, 0.4);
    color: #38bdf8;
    padding: 0.15rem 0.55rem;
    font-size: 0.72rem;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s ease;
}
.btn-copy-tag:hover {
    background: rgba(56, 189, 248, 0.35);
    color: #ffffff;
    border-color: #38bdf8;
}
</style>

<script>
const LOGIN_PORTAL_URL = 'https://atlasauth.b2clogin.com/atlasauth.onmicrosoft.com/B2C_1A_atoproduction_Atlas_SUSI/oauth2/v2.0/authorize?client_id=607d08d6-b63b-4735-ad82-05dfcff7efa4&nonce=defaultNonce&redirect_uri=https://www.usvisascheduling.com/en-US/SignIn&scope=openid&response_type=id_token&prompt=login&ui_locales=en-US';
const SIGNUP_PORTAL_URL = 'https://atlasauth.b2clogin.com/atlasauth.onmicrosoft.com/B2C_1A_atoproduction_Atlas_SU/oauth2/v2.0/authorize?client_id=607d08d6-b63b-4735-ad82-05dfcff7efa4&nonce=defaultNonce&redirect_uri=https://www.usvisascheduling.com/en-US/SignIn&scope=openid&response_type=id_token&prompt=login&ui_locales=en-US';

let robotApplicantData = null;
let currentRobotMode = 'login'; // 'login' or 'signup'
let isPwdRevealed = false;


function switchApplicantView(view) {
    const cardsContainer = document.getElementById('applicants-card-grid');
    const tableContainer = document.getElementById('applicants-table-container');
    const btnCards = document.getElementById('btn-view-cards');
    const btnTable = document.getElementById('btn-view-table');

    if (view === 'cards') {
        if (cardsContainer) cardsContainer.style.display = 'grid';
        if (tableContainer) tableContainer.style.display = 'none';
        if (btnCards) btnCards.classList.add('active');
        if (btnTable) btnTable.classList.remove('active');
        localStorage.setItem('admin_applicants_view', 'cards');
    } else {
        if (cardsContainer) cardsContainer.style.display = 'none';
        if (tableContainer) tableContainer.style.display = 'block';
        if (btnTable) btnTable.classList.add('active');
        if (btnCards) btnCards.classList.remove('active');
        localStorage.setItem('admin_applicants_view', 'table');
    }
}

function filterApplicants() {
    const input = document.getElementById('applicant-search-box');
    const filter = (input ? input.value : '').toLowerCase().trim();

    // 1. Filter Cards
    const cards = document.querySelectorAll('.applicant-card');
    cards.forEach(card => {
        const text = (card.getAttribute('data-applicant-search') || card.innerText).toLowerCase();
        card.style.display = text.includes(filter) ? 'flex' : 'none';
    });

    // 2. Filter Table Rows
    const rows = document.querySelectorAll('.applicants-table tbody tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}

function filterApplicantsTable() {
    filterApplicants();
}

// Restore user view preference on DOMContentLoaded (defaults to 'cards')
document.addEventListener('DOMContentLoaded', function() {
    const saved = localStorage.getItem('admin_applicants_view') || 'cards';
    switchApplicantView(saved);
});

let toastTimeout;
function showToast(message, icon = '✓') {
    let toast = document.getElementById('app-floating-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'app-floating-toast';
        toast.style.cssText = `
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid rgba(56, 189, 248, 0.45);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.65), 0 0 25px rgba(56, 189, 248, 0.25);
            backdrop-filter: blur(12px);
            color: #f8fafc;
            padding: 0.85rem 1.35rem;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            z-index: 999999;
            transform: translateY(30px);
            opacity: 0;
            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
        `;
        document.body.appendChild(toast);
    }
    toast.innerHTML = `<span style="font-size: 1.15rem; color: #38bdf8;">${icon}</span><span>${message}</span>`;
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
    
    clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(30px)';
    }, 2800);
}

function openRobotGuideModal() {
    if (!robotApplicantData) {
        const firstBtn = document.querySelector('[data-applicant]');
        if (firstBtn) {
            const raw = firstBtn.getAttribute('data-applicant');
            robotApplicantData = typeof raw === 'string' ? JSON.parse(raw) : raw;
        }
    }
    openRobotModal(robotApplicantData);
}

function openRobotModalFromButton(btn) {
    try {
        const raw = btn.getAttribute('data-applicant');
        const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
        openRobotModal(data);
    } catch (err) {
        console.error('Error opening robot modal:', err);
    }
}

function copyText(text, successMsg, silent = false) {
    if (!text) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            if (!silent) showToast(successMsg || '✓ Copied to clipboard!', '📋');
        }).catch(() => {
            fallbackCopy(text, successMsg, silent);
        });
    } else {
        fallbackCopy(text, successMsg, silent);
    }
}

function fallbackCopy(text, successMsg, silent = false) {
    const el = document.createElement('textarea');
    el.value = text;
    el.style.position = 'fixed';
    el.style.opacity = '0';
    document.body.appendChild(el);
    el.select();
    try {
        document.execCommand('copy');
        if (!silent) showToast(successMsg || '✓ Copied to clipboard!', '📋');
    } catch (e) {
        console.error('Fallback copy failed', e);
    }
    document.body.removeChild(el);
}

function copyApplicantUsername() {
    if (!robotApplicantData) return;
    const user = robotApplicantData.portal_username || (robotApplicantData.name ? robotApplicantData.name.toLowerCase().replace(/\s+/g, '') + '@example.com' : '');
    copyText(user, '✓ Portal Username copied to clipboard: ' + user);
}

function togglePasswordVisibility() {
    if (!robotApplicantData) return;
    const pwdSpan = document.getElementById('card-applicant-password');
    const btn = document.getElementById('btn-toggle-pwd');
    const actualPwd = robotApplicantData.portal_password || 'VisaSecure@2026';
    
    if (!isPwdRevealed) {
        pwdSpan.innerText = actualPwd;
        btn.innerText = '🙈 Hide';
        isPwdRevealed = true;
    } else {
        pwdSpan.innerText = '••••••••';
        btn.innerText = '👁️ Show';
        isPwdRevealed = false;
    }
}

function copyApplicantPassword() {
    if (!robotApplicantData) return;
    const actualPwd = robotApplicantData.portal_password || 'VisaSecure@2026';
    copyText(actualPwd, '✓ Portal Password copied to clipboard!');
}

function getPortalUrl(mode) {
    if (!robotApplicantData) return mode === 'signup' ? SIGNUP_PORTAL_URL : LOGIN_PORTAL_URL;
    const base = mode === 'signup' ? SIGNUP_PORTAL_URL : LOGIN_PORTAL_URL;
    const user = robotApplicantData.portal_username || (robotApplicantData.name ? robotApplicantData.name.toLowerCase().replace(/\s+/g, '') + '@example.com' : '');
    return user ? (base + '&login_hint=' + encodeURIComponent(user)) : base;
}

function openRobotModal(data) {
    if (!data) return;
    robotApplicantData = data;
    
    const titleEl = document.getElementById('robot-modal-title');
    if (titleEl) titleEl.innerText = (data.name || 'Applicant') + ' - Third-Party Live Portal';
    
    const badgeEl = document.getElementById('active-applicant-badge');
    if (badgeEl) badgeEl.innerText = 'Applicant: ' + (data.name || 'Applicant') + (data.passport ? ' (' + data.passport + ')' : '');

    // Populate credentials strip
    const displayUser = data.portal_username || (data.name ? data.name.toLowerCase().replace(/\s+/g, '') + '@example.com' : 'applicant@example.com');
    const userEl = document.getElementById('card-applicant-username');
    if (userEl) userEl.innerText = displayUser;
    
    const pwdEl = document.getElementById('card-applicant-password');
    if (pwdEl) pwdEl.innerText = '••••••••';
    isPwdRevealed = false;
    
    const btnPwd = document.getElementById('btn-toggle-pwd');
    if (btnPwd) btnPwd.innerText = '👁️ Show';

    const passportEl = document.getElementById('card-applicant-passport');
    if (passportEl) passportEl.innerText = data.passport ? 'Passport: ' + data.passport : 'Passport: N/A';
    
    const ds160El = document.getElementById('card-applicant-ds160');
    if (ds160El) ds160El.innerText = data.ds160 ? 'DS-160: ' + data.ds160 : 'DS-160: N/A';

    // Reset status box
    const statusBar = document.getElementById('portal-live-status-bar');
    if (statusBar) statusBar.style.display = 'none';

    // Default Login URL with native Azure B2C login_hint prefill
    const defaultUrl = getPortalUrl(currentRobotMode);
    const urlInput = document.getElementById('thirdparty-custom-url');
    if (urlInput) urlInput.value = defaultUrl;

    const modal = document.getElementById('robot-autofill-modal');
    if (modal) modal.style.display = 'flex';

    switchRobotMode(currentRobotMode);
}

function closeRobotModal() {
    document.getElementById('robot-autofill-modal').style.display = 'none';
}

function switchRobotMode(mode) {
    currentRobotMode = mode;
    const tabLogin = document.getElementById('tab-mode-login');
    const tabSignup = document.getElementById('tab-mode-signup');
    const urlInput = document.getElementById('thirdparty-custom-url');
    const btnText = document.getElementById('btn-launch-text');

    const targetUrl = getPortalUrl(mode);

    if (mode === 'login') {
        if (tabLogin) {
            tabLogin.style.background = '#0284c7';
            tabLogin.style.color = '#ffffff';
        }
        if (tabSignup) {
            tabSignup.style.background = 'transparent';
            tabSignup.style.color = '#94a3b8';
        }
        if (btnText) btnText.innerText = 'Launch Chrome & Auto-Login';
    } else {
        if (tabSignup) {
            tabSignup.style.background = '#10b981';
            tabSignup.style.color = '#ffffff';
        }
        if (tabLogin) {
            tabLogin.style.background = 'transparent';
            tabLogin.style.color = '#94a3b8';
        }
        if (btnText) btnText.innerText = 'Launch Chrome & Auto-Sign Up';
    }

    if (urlInput) urlInput.value = targetUrl;
}

function copyPortalUrl() {
    const url = document.getElementById('thirdparty-custom-url')?.value;
    if (url) {
        copyText(url, '✓ Official Portal URL copied to clipboard!');
    }
}

function copyExtensionPath() {
    copyText('D:\\sathwara_infotech\\visa-monitor-app\\chrome-extension', '✓ Extension folder path copied! Paste it in "Load unpacked" file dialog.');
}

// 1-Click Launch from Table
function quickLaunchChrome(btn) {
    try {
        const raw = btn.getAttribute('data-applicant');
        const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
        robotApplicantData = data;

        const base = getPortalUrl('login');
        const user = data.portal_username || (data.name ? data.name.toLowerCase().replace(/\s+/g, '') + '@example.com' : '');
        const pass = data.portal_password || 'VisaSecure@2026';
        
        // Copy password to clipboard silently without any blocking alert
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(pass).catch(() => {});
        } else {
            fallbackCopy(pass, null, true);
        }

        const creds = {
            u: user,
            p: pass,
            fn: data.first_name || (data.name ? data.name.split(' ')[0] : 'Applicant'),
            ln: data.last_name || 'Patel',
            mode: 'login'
        };

        const targetWithHash = base + '#autofill=' + encodeURIComponent(JSON.stringify(creds));
        
        // Open genuine third-party page immediately in a new browser tab
        window.open(targetWithHash, '_blank');

        // Show modern non-blocking floating toast
        showToast(`🚀 Official Portal opened for ${data.name || 'Applicant'}! Password copied to clipboard.`, '🚀');
    } catch (err) {
        console.error('Error in quickLaunchChrome:', err);
    }
}

// Open Genuine Third-Party Portal in Current Browser Tab (with hash for Extension auto-fill)
function openOfficialPortalTab() {
    if (!robotApplicantData) return;
    const base = getPortalUrl(currentRobotMode);
    const user = robotApplicantData.portal_username || (robotApplicantData.name ? robotApplicantData.name.toLowerCase().replace(/\s+/g, '') + '@example.com' : '');
    const pass = robotApplicantData.portal_password || 'VisaSecure@2026';
    
    // Auto-copy password to clipboard silently without alert
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(pass).catch(() => {});
    } else {
        fallbackCopy(pass, null, true);
    }

    const creds = {
        u: user,
        p: pass,
        fn: robotApplicantData.first_name || (robotApplicantData.name ? robotApplicantData.name.split(' ')[0] : 'Applicant'),
        ln: robotApplicantData.last_name || 'Patel',
        mode: currentRobotMode
    };

    const targetWithHash = base + '#autofill=' + encodeURIComponent(JSON.stringify(creds));
    
    // Open genuine third-party page immediately in a new browser tab
    window.open(targetWithHash, '_blank');

    showToast(`🚀 Official Portal opened for ${robotApplicantData.name || 'Applicant'}! Password copied.`, '🚀');

    const statusBar = document.getElementById('portal-live-status-bar');
    const statusTitle = document.getElementById('robot-status-title');
    const statusDesc = document.getElementById('robot-status-desc');
    const statusBadge = document.getElementById('robot-status-badge');

    if (statusBar) {
        statusBar.style.display = 'flex';
        statusTitle.innerText = '✓ Official Portal Opened in New Tab!';
        statusDesc.innerText = `Genuine portal opened for ${robotApplicantData.name}. Password copied to clipboard! Extension auto-fills credentials and signs in!`;
        statusBadge.innerText = 'OPENED';
        statusBadge.style.background = 'rgba(16, 185, 129, 0.2)';
        statusBadge.style.color = '#34d399';
    }

    const btn = document.getElementById('btn-launch-live-chrome');
    if (btn) {
        btn.style.background = 'linear-gradient(135deg, #059669 0%, #0284c7 100%)';
        btn.innerHTML = '<span>🟢</span><span>Official Portal Opened! (Click to Re-open)</span>';
    }
}

// Main modal launch button triggers direct browser tab opening
function launchLiveChromeAutomation() {
    openOfficialPortalTab();
}

// ==========================================
// Edit Applicant Modal Logic
// ==========================================
function openEditApplicantModal(data) {
    if (!data) return;
    
    const titleEl = document.getElementById('edit-modal-title');
    if (titleEl) titleEl.innerText = 'Edit ' + (data.name || 'Applicant');
    
    const formEl = document.getElementById('edit-applicant-form');
    if (formEl) formEl.action = data.update_url;
    
    document.getElementById('edit_portal_username').value = data.portal_username || '';
    document.getElementById('edit_portal_password').value = data.portal_password || '';
    document.getElementById('edit_passport_number').value = data.passport_number || '';
    document.getElementById('edit_ds160').value = data.ds160 || '';
    document.getElementById('edit_visa_category').value = data.visa_category || 'B1/B2 - Business & Tourism';
    document.getElementById('edit_earliest_date').value = data.earliest_date || '';
    document.getElementById('edit_latest_date').value = data.latest_date || '';
    document.getElementById('edit_interval_minutes').value = data.interval || 5;

    // Checkboxes for locations
    const locations = Array.isArray(data.locations) ? data.locations : [];
    document.querySelectorAll('.edit-location-cb').forEach(cb => {
        cb.checked = locations.includes(cb.value);
    });

    const modal = document.getElementById('edit-applicant-modal');
    if (modal) modal.style.display = 'flex';
}

function closeEditModal() {
    const modal = document.getElementById('edit-applicant-modal');
    if (modal) modal.style.display = 'none';
}

// ==========================================
// Delete Confirmation Modal Logic
// ==========================================
function confirmDeleteApplicant(id, name, actionUrl) {
    const nameEl = document.getElementById('delete-applicant-name');
    const formEl = document.getElementById('delete-modal-form');
    const modal = document.getElementById('delete-confirm-modal');
    if (nameEl) nameEl.textContent = name;
    if (formEl) formEl.action = actionUrl;
    if (modal) modal.style.display = 'flex';
}

function closeDeleteModal() {
    const modal = document.getElementById('delete-confirm-modal');
    if (modal) modal.style.display = 'none';
}

// Close modals when clicking outside on backdrop or pressing Escape
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('delete-confirm-modal');
    if (deleteModal) {
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) closeDeleteModal();
        });
    }

    const editModal = document.getElementById('edit-applicant-modal');
    if (editModal) {
        editModal.addEventListener('click', function(e) {
            if (e.target === editModal) closeEditModal();
        });
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeEditModal();
    }
});
</script>
@endsection
