<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Tidak Ada Koneksi — SIPRT</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="stylesheet" href="/css/public.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text);
            padding: 1.25rem;
        }

        /* ── Brand bar ── */
        .brand-bar {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-bottom: 2.5rem;
        }
        .brand-bar img { width: 32px; height: 32px; border-radius: 8px; }
        .brand-name { font-size: 1.1rem; font-weight: 800; color: var(--blue); letter-spacing: .04em; }

        /* ── Main card ── */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 2.25rem 1.75rem 2rem;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(0,0,0,.08);
            text-align: center;
        }

        /* ── Animated icon ── */
        .icon-wrap {
            position: relative;
            width: 88px;
            height: 88px;
            margin: 0 auto 1.5rem;
        }
        .icon-bg {
            position: relative;
            z-index: 1;
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: #EBF2FF;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .icon-bg svg { color: var(--blue); opacity: .7; }
        .pulse-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid var(--blue);
            opacity: 0;
            animation: pulse 2.4s ease-out infinite;
        }
        .pulse-ring:nth-child(2) { animation-delay: .8s; }
        .pulse-ring:nth-child(3) { animation-delay: 1.6s; }
        @keyframes pulse {
            0%   { transform: scale(1); opacity: .4; }
            100% { transform: scale(1.7); opacity: 0; }
        }

        /* ── Status badge ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: .25rem .75rem;
            border-radius: 20px;
            margin-bottom: 1rem;
        }
        .status-badge.offline {
            background: #FFF3CD;
            color: #856404;
            border: 1px solid #FFE082;
        }
        .status-badge.online {
            background: #D4EDDA;
            color: #155724;
            border: 1px solid #C3E6CB;
        }
        .status-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: currentColor;
        }
        .status-dot.blink { animation: blink 1.2s step-end infinite; }
        @keyframes blink { 50% { opacity: 0; } }

        .card-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: .45rem;
        }
        .card-sub {
            font-size: .875rem;
            color: var(--text-secondary);
            line-height: 1.65;
            margin-bottom: 1.75rem;
        }

        /* ── Feature rows ── */
        .features { display: flex; flex-direction: column; gap: .75rem; margin-bottom: 1.75rem; }
        .feat-row {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            background: var(--bg);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            padding: .75rem .9rem;
            text-align: left;
        }
        .feat-icon {
            width: 36px; height: 36px;
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 1rem;
        }
        .feat-icon.green { background: #E8F5E9; color: #2E7D32; }
        .feat-icon.red   { background: #FFEBEE; color: #c62828; }
        .feat-label { font-size: .8rem; font-weight: 700; color: var(--text); }
        .feat-desc  { font-size: .75rem; color: var(--text-secondary); margin-top: .15rem; line-height: 1.5; }

        /* ── Divider ── */
        .divider {
            display: flex; align-items: center; gap: .6rem;
            margin-bottom: 1.75rem; color: var(--text-secondary); font-size: .72rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .05em;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }

        /* ── Buttons ── */
        .btn-retry {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: var(--blue);
            color: #fff;
            border: none;
            border-radius: var(--radius-md);
            padding: .7rem 1.5rem;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            transition: background .15s, transform .1s;
        }
        .btn-retry:hover  { background: var(--blue-dark); }
        .btn-retry:active { transform: scale(.97); }
        .btn-retry svg { transition: transform .4s; }
        .btn-retry.spinning svg { animation: spin .8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Online toast ── */
        .toast-online {
            display: none;
            position: fixed;
            top: 1rem; left: 50%; transform: translateX(-50%);
            background: #1B5E20;
            color: #fff;
            font-size: .85rem;
            font-weight: 600;
            padding: .65rem 1.25rem;
            border-radius: var(--radius-md);
            box-shadow: 0 4px 20px rgba(0,0,0,.2);
            z-index: 999;
            white-space: nowrap;
            animation: slideDown .3s ease;
        }
        @keyframes slideDown { from { opacity: 0; top: -.5rem; } to { opacity: 1; top: 1rem; } }

        /* ── Footer ── */
        .footer-note {
            margin-top: 1.75rem;
            font-size: .7rem;
            color: var(--text-secondary);
            text-align: center;
            line-height: 1.6;
        }
        .footer-note strong { color: var(--blue); }

        @media (min-width: 480px) {
            .card { padding: 2.75rem 2.5rem 2.25rem; }
        }
    </style>
</head>
<body>

    <div class="toast-online" id="toastOnline">
        ✓ Koneksi kembali — mengalihkan…
    </div>

    <div class="brand-bar">
        <img src="/favicon/android-chrome-192x192.png" alt="SIPRT">
        <span class="brand-name">SIPRT</span>
    </div>

    <div class="card">

        {{-- Animated icon --}}
        <div class="icon-wrap">
            <div class="pulse-ring"></div>
            <div class="pulse-ring"></div>
            <div class="pulse-ring"></div>
            <div class="icon-bg">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="1" y1="1" x2="23" y2="23"/>
                    <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/>
                    <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/>
                    <path d="M10.71 5.05A16 16 0 0 1 22.56 9"/>
                    <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/>
                    <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                    <line x1="12" y1="20" x2="12.01" y2="20"/>
                </svg>
            </div>
        </div>

        {{-- Status badge --}}
        <div class="status-badge offline" id="statusBadge">
            <span class="status-dot blink"></span>
            <span id="statusText">Tidak Ada Koneksi</span>
        </div>

        <div class="card-title">Anda sedang offline</div>
        <div class="card-sub">
            Tidak dapat terhubung ke server SIPRT.<br>
            Beberapa fitur tetap tersedia saat offline.
        </div>

        {{-- Feature availability --}}
        <div class="features">
            <div class="feat-row">
                <div class="feat-icon green">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="feat-text">
                    <div class="feat-label">Halaman yang sudah dibuka</div>
                    <div class="feat-desc">Halaman yang pernah dikunjungi tersimpan di cache dan dapat dibuka kembali.</div>
                </div>
            </div>
            <div class="feat-row">
                <div class="feat-icon green">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="feat-text">
                    <div class="feat-label">Antrian laporan offline</div>
                    <div class="feat-desc">Laporan yang disimpan saat offline akan otomatis dikirim saat koneksi kembali.</div>
                </div>
            </div>
            <div class="feat-row">
                <div class="feat-icon red">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </div>
                <div class="feat-text">
                    <div class="feat-label">Data real-time & notifikasi</div>
                    <div class="feat-desc">Memerlukan koneksi internet. Data mungkin tidak terkini hingga kembali online.</div>
                </div>
            </div>
        </div>

        <div class="divider">Coba koneksi ulang</div>

        <button class="btn-retry" id="btnRetry" onclick="retryConnection()">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
            Coba Lagi
        </button>

    </div>

    <div class="footer-note">
        <strong>SIPRT</strong> — Sistem Informasi Penugasan &amp; Pelaporan Teknisi<br>
        Halaman ini tersimpan secara lokal &bull; Koneksi dipantau otomatis
    </div>

<script>
(function () {
    var badge   = document.getElementById('statusBadge');
    var badgeTxt= document.getElementById('statusText');
    var toast   = document.getElementById('toastOnline');
    var btn     = document.getElementById('btnRetry');

    function setOnline() {
        badge.className = 'status-badge online';
        badge.querySelector('.status-dot').classList.remove('blink');
        badgeTxt.textContent = 'Koneksi Tersedia';
        toast.style.display = 'block';
        setTimeout(function () { window.location.reload(); }, 1800);
    }

    function setOffline() {
        badge.className = 'status-badge offline';
        badge.querySelector('.status-dot').classList.add('blink');
        badgeTxt.textContent = 'Tidak Ada Koneksi';
        toast.style.display = 'none';
        btn.classList.remove('spinning');
        btn.disabled = false;
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg> Coba Lagi';
    }

    window.retryConnection = function () {
        btn.classList.add('spinning');
        btn.disabled = true;
        // Ping the server with a tiny cachebust request
        fetch('/?_offline_check=' + Date.now(), { method: 'HEAD', cache: 'no-store' })
            .then(function (r) { if (r.ok) { setOnline(); } else { setOffline(); } })
            .catch(function () { setOffline(); });
    };

    window.addEventListener('online',  function () { setOnline(); });
    window.addEventListener('offline', function () { setOffline(); });

    // Auto-ping every 8 seconds while on this page
    setInterval(function () {
        if (!navigator.onLine) return;
        fetch('/?_offline_check=' + Date.now(), { method: 'HEAD', cache: 'no-store' })
            .then(function (r) { if (r.ok) setOnline(); })
            .catch(function () {});
    }, 8000);

    // If browser already thinks we're online, try immediately
    if (navigator.onLine) {
        setTimeout(retryConnection, 600);
    }
})();
</script>
</body>
</html>
