<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline — SIPRT</title>
    <link rel="stylesheet" href="/css/public.css">
    <style>
        body { background: var(--bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .offline-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 2.5rem 2rem;
            max-width: 360px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,.07);
        }
        .offline-icon {
            font-size: 3.5rem;
            color: var(--blue);
            opacity: .25;
            margin-bottom: 1rem;
        }
        .offline-title { font-size: 1.15rem; font-weight: 700; color: var(--text); margin-bottom: .5rem; }
        .offline-sub   { font-size: .85rem; color: var(--text-secondary); margin-bottom: 1.5rem; }
        .offline-retry {
            display: inline-flex; align-items: center; gap: .4rem;
            background: var(--blue); color: #fff;
            border: none; border-radius: var(--radius-md);
            padding: .6rem 1.25rem; font-size: .875rem; font-weight: 600;
            cursor: pointer; text-decoration: none;
            transition: background .15s;
        }
        .offline-retry:hover { background: var(--blue-dark); }
        .offline-brand { margin-top: 2rem; font-size: .7rem; color: var(--text-secondary); font-weight: 600; letter-spacing: .06em; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="offline-card">
        <div class="offline-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 18h.01"/><path d="M9.172 15.172a4 4 0 0 1 5.656 0"/><path d="M6.343 12.343A8 8 0 0 1 12 10c.34 0 .677.02 1.008.057"/><path d="M3.515 9.515a12 12 0 0 1 10.14-3.447"/><path d="M3 3l18 18"/></svg>
        </div>
        <div class="offline-title">Tidak Ada Koneksi</div>
        <div class="offline-sub">Halaman ini tidak tersedia secara offline. Periksa koneksi internet Anda dan coba lagi.</div>
        <button class="offline-retry" onclick="window.location.reload()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
            Coba Lagi
        </button>
        <div class="offline-brand">SIPRT</div>
    </div>
</body>
</html>
