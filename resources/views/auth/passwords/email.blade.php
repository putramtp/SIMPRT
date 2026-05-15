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
        <h1 class="login-title">{{ __('Forgot Password?') }}</h1>
        <p class="login-subtitle">{{ __("Enter your email and we'll send you a reset link") }}</p>

        {{-- Status message --}}
        @if (session('status'))
            <div class="login-alert-success">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('status') }}
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('password.email') }}" novalidate>
            @csrf

            <div class="login-field">
                <label for="email" class="login-label">{{ __('Email Address') }}</label>
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

            <button type="submit" class="login-btn">
                {{ __('Send Reset Link') }}
                <i class="bi bi-send ms-2"></i>
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
