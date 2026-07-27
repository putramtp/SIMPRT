<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1565C0">
    <title>{{ $pageTitle ?? config('app.name', 'SIPRT') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">

    <style>
        body { background: var(--bg); }
        .pub-topbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--topbar-h);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 1rem;
            gap: .75rem;
            z-index: 1000;
        }
        .pub-topbar-brand {
            font-size: 1rem;
            font-weight: 700;
            color: var(--blue);
            display: flex;
            align-items: center;
            gap: .4rem;
            text-decoration: none;
        }
        .pub-topbar-badge {
            font-size: .65rem;
            font-weight: 600;
            background: var(--yellow-light);
            color: var(--orange);
            border-radius: 20px;
            padding: 2px 8px;
            margin-left: auto;
        }
        .pub-content { padding-top: var(--topbar-h); min-height: 100vh; }
    </style>
    @yield('css')
</head>
<body>

<header class="pub-topbar no-print">
    <a href="/" class="pub-topbar-brand">
        <i class="ti ti-tool"></i>
        <span>SIPRT</span>
    </a>
    <span class="pub-topbar-badge"><i class="ti ti-lock me-1"></i>Link Terbatas</span>
</header>

<div class="pub-content">
    @yield('content')
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
@yield('js')
</body>
</html>
