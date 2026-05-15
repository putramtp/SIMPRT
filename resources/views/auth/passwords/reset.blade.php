@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
{{-- Animated background --}}
<div class="login-bg">
    <div class="login-grid"></div>
    <div class="login-bg-orb"></div>
</div>

{{-- Page wrapper --}}
<div class="login-wrapper">
    <div class="login-card">

        {{-- Brand --}}
        <div class="login-brand">
            <img src="{{ asset('favicon/SIPRT.png') }}" alt="{{ config('app.name', 'SIPRT') }}" class="login-brand-logo" height="40" width="auto">
        </div>

        {{-- Heading --}}
        <h1 class="login-title">{{ __('Reset Password') }}</h1>
        <p class="login-subtitle">{{ __('Enter your new password below') }}</p>

        {{-- Form --}}
        <form method="POST" action="{{ route('password.update') }}" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <div class="login-field">
                <label for="email" class="login-label">{{ __('Email Address') }}</label>
                <input
                    id="email"
                    type="email"
                    class="login-input @error('email') is-invalid @enderror"
                    name="email"
                    value="{{ $email ?? old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                    placeholder="you@example.com"
                >
                @error('email')
                    <div class="login-invalid-feedback">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- New Password --}}
            <div class="login-field">
                <label for="password" class="login-label">{{ __('New Password') }}</label>
                <div class="input-eye-wrap">
                    <input
                        id="password"
                        type="password"
                        class="login-input @error('password') is-invalid @enderror"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    >
                    <button
                        type="button"
                        class="input-eye-btn"
                        id="togglePassword"
                        aria-label="{{ __('Toggle password visibility') }}"
                    >
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="login-invalid-feedback">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="login-field">
                <label for="password-confirm" class="login-label">{{ __('Confirm Password') }}</label>
                <div class="input-eye-wrap">
                    <input
                        id="password-confirm"
                        type="password"
                        class="login-input"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    >
                    <button
                        type="button"
                        class="input-eye-btn"
                        id="toggleConfirm"
                        aria-label="{{ __('Toggle confirm password visibility') }}"
                    >
                        <i class="bi bi-eye" id="eyeIconConfirm"></i>
                    </button>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="login-btn">
                {{ __('Reset Password') }}
                <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </form>

        <p class="login-footer">
            <a href="{{ route('login') }}">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Sign In') }}
            </a>
        </p>

    </div>
</div>
@endsection

@section('js')
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eyeIcon');
        const show  = input.type === 'password';
        input.type  = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });

    document.getElementById('toggleConfirm').addEventListener('click', function () {
        const input = document.getElementById('password-confirm');
        const icon  = document.getElementById('eyeIconConfirm');
        const show  = input.type === 'password';
        input.type  = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
</script>
@endsection
