@extends('layouts.app')

@section('title', 'Admin Sign In - Sathwara InfoTech')

@section('styles')
<style>
    html, body {
        height: 100vh !important;
        overflow: hidden !important;
    }
    .footer {
        display: none !important;
    }
    .main-container {
        padding: 0 1rem !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: calc(100vh - 65px) !important;
        height: calc(100vh - 65px) !important;
        overflow: hidden !important;
    }
</style>
@endsection

@section('content')
<div style="max-width: 420px; width: 100%; margin: 0 auto;">
    <div class="glass-card" style="border: 1px solid rgba(16, 185, 129, 0.25); padding: 1.75rem 2rem; border-radius: 16px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 46px; height: 46px; border-radius: 12px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #34d399;">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
            </div>
            <h2 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 0.25rem; color: var(--text-primary);">Admin Sign In</h2>
            <p style="color: var(--text-secondary); font-size: 0.84rem; margin: 0;">Sathwara InfoTech Command Center</p>
        </div>

        @if(session('error'))
            <div style="padding: 0.6rem 0.9rem; border-radius: 8px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; font-size: 0.82rem; margin-bottom: 1rem;">
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div style="padding: 0.6rem 0.9rem; border-radius: 8px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); color: var(--accent-cyan); font-size: 0.82rem; margin-bottom: 1rem;">
                {{ session('info') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="admin-email" style="font-size: 0.82rem; margin-bottom: 0.3rem;">Administrator Email</label>
                <input type="email" id="admin-email" name="email" value="{{ old('email') }}" required autofocus class="form-input" placeholder="admin@example.com" style="padding: 0.65rem 0.9rem;">
                @error('email')
                    <div style="color: var(--accent-rose); font-size: 0.78rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.15rem;">
                <label class="form-label" for="admin-password" style="font-size: 0.82rem; margin-bottom: 0.3rem;">Administrator Password</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" id="admin-password" name="password" required class="form-input" placeholder="••••••••" style="padding: 0.65rem 2.85rem 0.65rem 0.9rem;">
                    <button type="button" id="admin-toggle-password-btn" aria-label="Toggle password visibility" style="position: absolute; right: 0.75rem; background: transparent; border: none; cursor: pointer; color: var(--text-muted); padding: 0.25rem; display: flex; align-items: center; justify-content: center; transition: color 0.2s;" onmouseover="this.style.color='#34d399'" onmouseout="this.style.color='var(--text-muted)'">
                        <svg id="admin-eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg id="admin-eye-off-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <div style="color: var(--accent-rose); font-size: 0.78rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; font-size: 0.82rem;">
                <label style="display: flex; align-items: center; gap: 0.45rem; cursor: pointer; color: var(--text-secondary);">
                    <input type="checkbox" name="remember" style="accent-color: #10b981;">
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn btn-primary" id="admin-login-submit-btn" style="width: 100%; padding: 0.72rem; font-size: 0.92rem; font-weight: 600; background: linear-gradient(135deg, #059669 0%, #0284c7 100%); border-color: rgba(16, 185, 129, 0.4);">
                Enter Command Center
            </button>
        </form>

        <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.08); text-align: center; font-size: 0.8rem; color: var(--text-muted);">
            Visa applicant? <a href="{{ route('login') }}" style="color: var(--accent-cyan); text-decoration: none; font-weight: 600;">Go to Customer Portal</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('admin-toggle-password-btn');
        const passwordInput = document.getElementById('admin-password');
        const eyeIcon = document.getElementById('admin-eye-icon');
        const eyeOffIcon = document.getElementById('admin-eye-off-icon');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                if (eyeIcon && eyeOffIcon) {
                    eyeIcon.style.display = isPassword ? 'none' : 'block';
                    eyeOffIcon.style.display = isPassword ? 'block' : 'none';
                }
            });
        }
    });
</script>
@endsection
