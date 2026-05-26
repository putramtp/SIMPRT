<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Customer — {{ config('app.name', 'SIPRT') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">

    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}?v={{ filemtime(public_path('css/public.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="pwa-body">

<main class="pwa-guest-main">

{{-- Animated background --}}
<div class="login-bg">
    <div class="login-grid"></div>
    <div class="login-bg-orb"></div>
</div>

<div class="login-wrapper">
    <div class="login-card">

        {{-- Brand --}}
        <div class="login-brand">
            <img src="{{ asset('favicon/SIPRT.png') }}" alt="{{ config('app.name', 'SIPRT') }}" class="login-brand-logo" height="40" width="auto">
        </div>

        {{-- Heading --}}
        <h1 class="login-title">Portal Customer</h1>
        <p class="login-subtitle">Masuk untuk melihat laporan pekerjaan Anda</p>

        {{-- Form --}}
        <form method="POST" action="{{ route('customer.login.submit') }}" novalidate>
            @csrf

            {{-- Email --}}
            <div class="login-field">
                <label for="email" class="login-label">Email</label>
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
                <label for="password" class="login-label">Password</label>
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
                    <button type="button" class="input-eye-btn" id="togglePassword" aria-label="Toggle password">
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

            {{-- Remember --}}
            <div class="login-meta">
                <label class="login-check">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="login-check-label">Ingat saya</span>
                </label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="login-btn">
                Masuk <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </form>

        {{-- Staff link --}}
        <p class="login-footer" style="margin-top:1.5rem;">
            Akun staff? <a href="{{ url('/login') }}">Login Staff →</a>
        </p>

    </div>
</div>

</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
(function () {
    var toggleBtn = document.getElementById('togglePassword');
    if (toggleBtn) {
        var pw = document.getElementById('password');
        var eyeIcon = document.getElementById('eyeIcon');
        toggleBtn.addEventListener('click', function () {
            var hidden = pw.type === 'password';
            pw.type = hidden ? 'text' : 'password';
            eyeIcon.classList.toggle('bi-eye', !hidden);
            eyeIcon.classList.toggle('bi-eye-slash', hidden);
        });
    }
})();
</script>
</body>
</html>
