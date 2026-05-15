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

        {{-- Icon --}}
        <div class="verify-icon">
            <i class="bi bi-envelope-check"></i>
        </div>

        {{-- Heading --}}
        <h1 class="login-title">{{ __('Check Your Email') }}</h1>
        <p class="login-subtitle">{{ __('We sent a verification link to your inbox. Click it to activate your account.') }}</p>

        {{-- Resent status --}}
        @if (session('resent'))
            <div class="login-alert-success">
                <i class="bi bi-check-circle-fill"></i>
                {{ __('A fresh verification link has been sent to your email address.') }}
            </div>
        @endif

        {{-- Resend form --}}
        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="login-btn">
                <i class="bi bi-arrow-clockwise me-2"></i>
                {{ __('Resend Verification Email') }}
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
