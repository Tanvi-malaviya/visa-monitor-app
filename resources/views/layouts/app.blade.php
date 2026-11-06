<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'U.S. Visa Appointment Monitoring System') | Sathwara InfoTech</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-base: #0a0f1d;
            --bg-surface: #111827;
            --bg-card: rgba(17, 24, 39, 0.75);
            --bg-card-hover: rgba(31, 41, 55, 0.85);
            --border-glass: rgba(255, 255, 255, 0.08);
            --border-accent: rgba(59, 130, 246, 0.3);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-blue: #3b82f6;
            --accent-cyan: #06b6d4;
            --accent-emerald: #10b981;
            --accent-amber: #f59e0b;
            --accent-rose: #f43f5e;
            --accent-indigo: #6366f1;
            --gradient-primary: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            --gradient-accent: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
            --gradient-gold: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --glow-blue: 0 0 25px rgba(59, 130, 246, 0.35);
            --glow-emerald: 0 0 25px rgba(16, 185, 129, 0.35);
            --glow-amber: 0 0 25px rgba(245, 158, 11, 0.35);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(59, 130, 246, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(6, 182, 212, 0.06) 0%, transparent 40%);
            background-attachment: fixed;
            -webkit-font-smoothing: antialiased;
        }

        /* Glassmorphism Navbar */
        .navbar {
            background: rgba(10, 15, 29, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-glass);
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 0.85rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: inherit;
        }

        .brand-logo-badge {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--glow-blue);
            color: white;
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
        }

        .brand-text h1 {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.2px;
        }

        .brand-text p {
            font-size: 0.72rem;
            color: var(--accent-cyan);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 0.55rem 1rem;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-glass);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Demo Quick Switcher */
        .demo-pill-group {
            display: flex;
            align-items: center;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-glass);
            border-radius: 10px;
            padding: 3px;
        }

        .demo-pill {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 7px;
            text-decoration: none;
            color: var(--text-muted);
            transition: all 0.2s;
        }

        .demo-pill.active {
            background: rgba(59, 130, 246, 0.25);
            color: var(--accent-cyan);
            border: 1px solid var(--border-accent);
        }

        .demo-pill:hover:not(.active) {
            color: var(--text-primary);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            font-size: 0.88rem;
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            border: none;
            outline: none;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
            filter: brightness(1.1);
        }

        .btn-emerald {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        }

        .btn-emerald:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
        }

        .btn-warning {
            background: var(--gradient-gold);
            color: #0f172a;
            font-weight: 700;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        .btn-outline {
            background: transparent;
            color: var(--text-primary);
            border: 1px solid var(--border-glass);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .btn-sm {
            padding: 0.4rem 0.85rem;
            font-size: 0.8rem;
            border-radius: 7px;
        }

        /* Main Container */
        .main-container {
            flex: 1;
            max-width: 1380px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        /* Glass Cards */
        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-glass);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
            transition: border-color 0.2s ease;
        }

        .glass-card:hover {
            border-color: rgba(255, 255, 255, 0.14);
        }

        /* Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-active {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-paused {
            background: rgba(148, 163, 184, 0.15);
            color: #cbd5e1;
            border: 1px solid rgba(148, 163, 184, 0.3);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
            animation: pulse-border 2s infinite;
        }

        .badge-success {
            background: rgba(6, 182, 212, 0.15);
            color: #22d3ee;
            border: 1px solid rgba(6, 182, 212, 0.3);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Live Indicator Pulse */
        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: currentColor;
            display: inline-block;
            box-shadow: 0 0 10px currentColor;
            animation: pulse 1.6s ease-in-out infinite alternate;
        }

        @keyframes pulse {
            0% { opacity: 0.4; transform: scale(0.85); }
            100% { opacity: 1; transform: scale(1.15); }
        }

        @keyframes pulse-border {
            0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }

        /* Flash Alerts */
        .alert-box {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.9rem;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #6ee7b7;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.4);
            color: #fde68a;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Footer */
        .footer {
            border-top: 1px solid var(--border-glass);
            background: rgba(10, 15, 29, 0.9);
            padding: 1.5rem 2rem;
            margin-top: auto;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .footer a {
            color: var(--accent-cyan);
            text-decoration: none;
        }

        /* Form Controls */
        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.4rem;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid var(--border-glass);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 0.92rem;
            outline: none;
            transition: all 0.2s;
        }

        .form-input:focus, .form-select:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .form-select option {
            background: var(--bg-surface);
            color: var(--text-primary);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Header Navbar -->
    <header class="navbar">
        <a href="/" class="brand-container" id="nav-brand-logo">
            <div class="brand-logo-badge">US</div>
            <div class="brand-text">
                <h1>Visa Slot Monitor</h1>
                <p>Sathwara InfoTech Engine</p>
            </div>
        </a>

        @auth
        <nav class="nav-links">
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" id="nav-admin-dashboard">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Command Center
                </a>
                <a href="{{ route('admin.customers') }}" class="nav-link {{ request()->routeIs('admin.customers') ? 'active' : '' }}" id="nav-admin-customers">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Customers & Applicants
                </a>
            @else
                <a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" id="nav-customer-dashboard">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    My Monitoring Portal
                </a>
            @endif
        </nav>
        @endauth

        <div class="nav-actions">
            @auth
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="text-align: right;">
                        <div style="font-size: 0.85rem; font-weight: 700;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 0.72rem; color: var(--accent-cyan); font-weight: 600;">{{ strtoupper(Auth::user()->role) }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm" id="logout-btn" title="Sign Out">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-container">
        @if(session('success'))
            <div class="alert-box alert-success" id="flash-success">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: inherit; cursor: pointer; font-size: 1.1rem;">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-box alert-error" id="flash-error">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: inherit; cursor: pointer; font-size: 1.1rem;">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p><strong>U.S. Visa Appointment Monitoring System</strong> &bull; Developed by <span style="color: var(--accent-cyan); font-weight: 600;">Sathwara InfoTech</span></p>
        <p style="margin-top: 0.4rem; font-size: 0.75rem; color: var(--text-muted);">
            Server-side scheduled worker architecture. Automated slot detection conforms to permitted access rules.
        </p>
    </footer>

    @yield('scripts')
</body>
</html>
