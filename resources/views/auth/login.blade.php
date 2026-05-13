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
            <div class="login-brand-icon">
                <i class="bi bi-hexagon-fill"></i>
            </div>
            <span class="login-brand-name">{{ config('app.name', 'AppName') }}</span>
        </div>

        {{-- Heading --}}
        <h1 class="login-title">{{ __('Welcome back') }}</h1>
        <p class="login-subtitle">{{ __('Sign in to continue to your account') }}</p>

        {{-- Form --}}
        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            {{-- Email --}}
            <div class="login-field">
                <label for="email" class="login-label">
                    {{ __('Email Address') }}
                </label>
                <input
                    id="email"
                    type="email"
                    class="login-input @error('email') is-invalid @enderror"
                    name="email"
                    value="{{ old('email') }}"
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

            {{-- Password --}}
            <div class="login-field">
                <label for="password" class="login-label">
                    {{ __('Password') }}
                </label>
                <div class="input-eye-wrap">
                    <input
                        id="password"
                        type="password"
                        class="login-input @error('password') is-invalid @enderror"
                        name="password"
                        required
                        autocomplete="current-password"
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

            {{-- Remember + Forgot --}}
            <div class="login-meta">
                <label class="login-check">
                    <input
                        type="checkbox"
                        name="remember"
                        id="remember"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <span class="login-check-label">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="login-forgot">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit" class="login-btn">
                {{ __('Sign In') }}
                <i class="bi bi-arrow-right ms-2"></i>
            </button>

        </form>

        {{-- Footer note (optional — show only if registration route exists) --}}
        @if (Route::has('register'))
            <p class="login-footer">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}">{{ __('Create one') }}</a>
            </p>
        @endif

    </div>
</div>
@endsection
