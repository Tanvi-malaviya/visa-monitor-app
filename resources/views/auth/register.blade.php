@extends('layouts.app')

@section('title', 'Register - Customer Portal')

@section('content')
<div style="max-width: 520px; margin: 2rem auto;">
    <div class="glass-card" style="border: 1px solid rgba(255, 255, 255, 0.12); padding: 2.25rem;">
        <div style="text-align: center; margin-bottom: 1.75rem;">
            <div class="brand-logo-badge" style="margin: 0 auto 0.75rem auto;">US</div>
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.35rem;">Customer Registration</h3>
            <p style="color: var(--text-secondary); font-size: 0.88rem;">Create your monitoring account to start 24/7 visa appointment tracking.</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div style="margin-bottom: 1.25rem;">
                <label class="form-label" for="name">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus class="form-input" placeholder="e.g. John Doe">
                @error('name')
                    <div style="color: var(--accent-rose); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="form-input" placeholder="name@example.com">
                @error('email')
                    <div style="color: var(--accent-rose); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label class="form-label" for="phone">Phone / WhatsApp Number</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required class="form-input" placeholder="+1 (555) 000-0000 or +91 98765 00000">
                @error('phone')
                    <div style="color: var(--accent-rose); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" required class="form-input" placeholder="Minimum 6 characters">
                @error('password')
                    <div style="color: var(--accent-rose); font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.75rem;">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="form-input" placeholder="Repeat password">
            </div>

            <button type="submit" class="btn btn-primary" id="register-submit-btn" style="width: 100%; padding: 0.8rem; font-size: 0.95rem;">
                Register & Setup Monitor
            </button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center; font-size: 0.85rem; color: var(--text-secondary);">
            Already have an account? <a href="{{ route('login') }}" style="color: var(--accent-blue); font-weight: 600; text-decoration: none;">Log In</a>
        </div>
    </div>
</div>
@endsection
