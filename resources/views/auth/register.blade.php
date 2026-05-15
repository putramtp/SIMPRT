@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')

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
        <h1 class="login-title">{{ __('Create Account') }}</h1>
        <p class="login-subtitle">{{ __('Fill in the details to get started') }}</p>

        {{-- Form --}}
        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="login-field">
                <label for="name" class="login-label">
                    {{ __('Name') }}
                </label>
                <input
                    id="name"
                    type="text"
                    class="login-input @error('name') is-invalid @enderror"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    autofocus
                    placeholder="John Doe"
                >
                @error('name')
                    <div class="login-invalid-feedback">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
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
                        autocomplete="new-password"
                        placeholder="Min. 8 characters"
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

            {{-- password-confirm --}}
            <div class="login-field">
                <label for="password_confirm" class="login-label">
                    {{ __('Confirm Password') }}
                </label>
                <div class="input-eye-wrap">
                    <input
                        id="password_confirm"
                        type="password"
                        class="login-input @error('password_confirmation') is-invalid @enderror"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Repeat your password"
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
                @error('password_confirmation')
                    <div class="login-invalid-feedback">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit" class="login-btn">
                {{ __('Create Account') }}
                <i class="bi bi-arrow-right ms-2"></i>
            </button>

        </form>

        {{-- Footer note (optional — show only if registration route exists) --}}
        @if (Route::has('register'))
            <p class="login-footer">
                {{ __("Already have an account?") }}
                <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
            </p>
        @endif

    </div>
</div>

@endsection
