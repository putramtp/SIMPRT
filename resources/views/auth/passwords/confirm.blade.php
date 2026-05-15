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
        <h1 class="login-title">{{ __('Confirm Password') }}</h1>
        <p class="login-subtitle">{{ __('Please confirm your password before continuing.') }}</p>

        {{-- Form --}}
        <form method="POST" action="{{ route('password.confirm') }}" novalidate>
            @csrf

            <div class="login-field">
                <label for="password" class="login-label">{{ __('Password') }}</label>
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

            <button type="submit" class="login-btn">
                {{ __('Confirm Password') }}
                <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </form>

        @if (Route::has('password.request'))
            <p class="login-footer">
                <a href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            </p>
        @endif

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
</script>
@endsection
