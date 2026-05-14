<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} — {{ config('app.name', 'SIPRT') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">

    <style>
        :root {
            --blue:        #1565C0;
            --blue-dark:   #0D47A1;
            --blue-light:  #E3F2FD;
            --text:        #1a1d23;
            --text-secondary: #7a8099;
            --border:      #e0e4ed;
            --bg:          #f4f6fb;
            --white:       #ffffff;
            --radius-lg:   14px;
            --radius-xl:   20px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Topbar ── */
        .err-topbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            height: 56px;
            display: flex;
            align-items: center;
            padding: 0 20px;
            gap: 10px;
            flex-shrink: 0;
        }
        .err-topbar-logo {
            width: 30px; height: 30px;
            background: var(--blue);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .err-topbar-logo img { width: 18px; height: 18px; }
        .err-topbar-brand {
            font-size: .95rem;
            font-weight: 700;
            color: var(--blue);
            letter-spacing: -.01em;
        }

        /* ── Main area ── */
        .err-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* ── Card ── */
        .err-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 2.5rem 2rem;
            max-width: 460px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 32px rgba(21,101,192,.07), 0 1px 4px rgba(0,0,0,.04);
        }

        /* ── Code badge ── */
        .err-code-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            position: relative;
        }
        .err-code-ring {
            width: 88px; height: 88px;
            border-radius: 50%;
            background: var(--blue-light);
            display: flex; align-items: center; justify-content: center;
            position: relative;
        }
        .err-code-ring::before {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 50%;
            border: 2px dashed rgba(21,101,192,.18);
        }
        .err-code-ring i {
            font-size: 2.4rem;
            color: var(--blue);
        }

        .err-code {
            font-size: 4rem;
            font-weight: 800;
            color: var(--blue);
            line-height: 1;
            letter-spacing: -.04em;
            margin-bottom: .25rem;
        }
        .err-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: .5rem;
        }
        .err-message {
            font-size: .875rem;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 1.75rem;
        }

        /* ── Divider ── */
        .err-divider {
            height: 1px;
            background: var(--border);
            margin: 0 auto 1.5rem;
            width: 48px;
            border-radius: 2px;
        }

        /* ── Actions ── */
        .err-actions {
            display: flex;
            flex-direction: column;
            gap: .625rem;
        }
        .err-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            background: var(--blue);
            color: #fff;
            border: none;
            border-radius: var(--radius-lg);
            padding: .6rem 1.25rem;
            font-size: .875rem;
            font-weight: 600;
            text-decoration: none;
            transition: background .18s, transform .15s, box-shadow .18s;
            cursor: pointer;
        }
        .err-btn-primary:hover {
            background: var(--blue-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(21,101,192,.28);
        }
        .err-btn-primary:active { transform: scale(.97); }

        .err-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: .6rem 1.25rem;
            font-size: .875rem;
            font-weight: 600;
            text-decoration: none;
            transition: border-color .18s, color .18s, background .18s;
            cursor: pointer;
        }
        .err-btn-secondary:hover {
            border-color: var(--blue);
            color: var(--blue);
            background: var(--blue-light);
        }

        /* ── Footer ── */
        .err-footer {
            text-align: center;
            padding: 1.25rem;
            font-size: .75rem;
            color: var(--text-secondary);
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        /* ── Background decoration ── */
        .err-bg-deco {
            position: fixed;
            pointer-events: none;
            z-index: 0;
            top: 56px; left: 0; right: 0; bottom: 0;
            overflow: hidden;
        }
        .err-bg-deco::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(21,101,192,.055) 0%, transparent 70%);
            top: -150px; right: -150px;
        }
        .err-bg-deco::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(21,101,192,.04) 0%, transparent 70%);
            bottom: -100px; left: -100px;
        }

        .err-main { position: relative; z-index: 1; }

        @media (max-width: 480px) {
            .err-card { padding: 2rem 1.25rem; border-radius: var(--radius-lg); }
            .err-code  { font-size: 3.5rem; }
        }

        @keyframes errIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .err-card { animation: errIn .35s ease both; }
    </style>
</head>
<body>

    {{-- Topbar --}}
    <header class="err-topbar">
        <div class="err-topbar-logo">
            <img src="{{ asset('favicon/android-chrome-192x192.png') }}" alt="SIPRT">
        </div>
        <span class="err-topbar-brand">SIPRT</span>
    </header>

    {{-- Background decoration --}}
    <div class="err-bg-deco"></div>

    {{-- Main --}}
    <main class="err-main">
        <div class="err-card">

            <div class="err-code-wrap">
                <div class="err-code-ring">
                    <i class="ti {{ $icon }}"></i>
                </div>
            </div>

            <div class="err-code">{{ $code }}</div>
            <div class="err-title">{{ $title }}</div>
            <div class="err-divider"></div>
            <div class="err-message">{{ $message }}</div>

            <div class="err-actions">
                @auth
                <a href="{{ url('/') }}" class="err-btn-primary">
                    <i class="ti ti-home"></i> Ke Beranda
                </a>
                <a href="javascript:history.back()" class="err-btn-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
                @else
                <a href="{{ route('login') }}" class="err-btn-primary">
                    <i class="ti ti-login"></i> Masuk
                </a>
                <a href="javascript:history.back()" class="err-btn-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
                @endauth
            </div>

        </div>
    </main>

    {{-- Footer --}}
    <footer class="err-footer">
        &copy; {{ date('Y') }} {{ config('app.name', 'SIPRT') }} &mdash; Sistem Informasi Penugasan &amp; Pelaporan Teknisi
    </footer>

</body>
</html>
