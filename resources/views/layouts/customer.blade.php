<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PWA Meta --}}
    <meta name="theme-color" content="#1565C0">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'SIPRT') }}">
    <meta name="description" content="Portal Laporan Customer — {{ config('app.name', 'SIPRT') }}">

    <title>{{ config('app.name', 'SIPRT') }}</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <link rel="stylesheet" href="{{ asset('css/public.css') }}?v={{ filemtime(public_path('css/public.css')) }}">
    @yield('css')
</head>
<body class="pwa-body">

@php $custUser = Auth::guard('customer')->user(); @endphp

{{-- ── Sidebar (tablet/desktop only) ── --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <img src="{{ asset('favicon/android-chrome-192x192.png') }}" alt="Logo" style="width:24px;height:24px">
        </div>
        <span>SIPRT</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('customer.dashboard') }}"
           class="nav-item {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
            <i class="ti ti-home"></i><span>Beranda</span>
        </a>
        <a href="{{ route('customer.laporan') }}"
           class="nav-item {{ request()->routeIs('customer.laporan*') ? 'active' : '' }}">
            <i class="ti ti-file-text"></i><span>Laporan Saya</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <button class="sidebar-user-toggle" id="sidebarUserToggle" aria-expanded="false">
            <div class="sidebar-avatar">{{ strtoupper(substr($custUser->name, 0, 1)) }}</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ $custUser->name }}</div>
                <div class="sidebar-user-role">customer</div>
            </div>
            <i class="ti ti-chevron-up sidebar-user-chevron"></i>
        </button>
        <div class="sidebar-user-menu" id="sidebarUserMenu">
            <a href="{{ route('customer.profile.signature.show') }}" class="sidebar-user-menu-item">
                <i class="ti ti-writing"></i><span>Tanda Tangan</span>
            </a>
            <a href="{{ route('customer.profile.password.show') }}" class="sidebar-user-menu-item">
                <i class="ti ti-lock"></i><span>Edit Password</span>
            </a>
            <form method="POST" action="{{ route('customer.logout') }}" class="mb-0">
                @csrf
                <button type="submit" class="sidebar-logout">
                    <i class="ti ti-logout"></i><span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ── Overlay for mobile sidebar ── --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ── Main wrapper ── --}}
<div class="pwa-main" id="pwaMain">

    {{-- Topbar --}}
    <header class="pwa-topbar" id="pwaTopbar">
        <button class="pwa-menu-btn" id="menuToggle" aria-label="Menu">
            <i class="ti ti-menu-2"></i>
        </button>
        <div class="pwa-topbar-icon">
            <img src="{{ asset('favicon/android-chrome-192x192.png') }}" alt="SIPRT" width="22" height="22">
        </div>
        <span class="pwa-topbar-title">{{ config('app.name', 'SIPRT') }}</span>
        <div class="pwa-topbar-right">
            <span class="pwa-topbar-user d-none d-md-block">{{ $custUser->name }}</span>
            <form method="POST" action="{{ route('customer.logout') }}" class="mb-0 d-none d-md-block">
                @csrf
                <button type="submit" class="pwa-logout-btn" title="Keluar">
                    <i class="ti ti-logout"></i>
                </button>
            </form>
        </div>
    </header>

    {{-- Page content --}}
    <main class="pwa-content">
        <div class="pwa-container">
            @yield('content')
        </div>
    </main>

</div>{{-- /.pwa-main --}}

{{-- ── Bottom Navigation (mobile only) ── --}}
<nav class="pwa-bottom-nav" id="bottomNav">
    <a href="{{ route('customer.dashboard') }}"
       class="pwa-bn-item {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
        <i class="ti ti-home"></i>
        <span>Beranda</span>
    </a>
    <a href="{{ route('customer.laporan') }}"
       class="pwa-bn-item {{ request()->routeIs('customer.laporan*') ? 'active' : '' }}">
        <i class="ti ti-file-text"></i>
        <span>Laporan</span>
    </a>
    <button type="button" class="pwa-bn-item"
            onclick="document.getElementById('custBnLogout').submit()"
            style="background:none;border:none;width:100%;cursor:pointer;">
        <i class="ti ti-logout"></i>
        <span>Keluar</span>
    </button>
    <form id="custBnLogout" method="POST" action="{{ route('customer.logout') }}" style="display:none;">@csrf</form>
</nav>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmF23EStqFUkBmZG1t5K3DNZX1ub"
        crossorigin="anonymous"></script>

<script>
(function () {
    var menuBtn = document.getElementById('menuToggle');
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');

    function openSidebar()  { sidebar?.classList.add('open'); overlay?.classList.add('show'); }
    function closeSidebar() { sidebar?.classList.remove('open'); overlay?.classList.remove('show'); }

    menuBtn?.addEventListener('click', function () {
        sidebar?.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    overlay?.addEventListener('click', closeSidebar);

    sidebar?.querySelectorAll('.nav-item').forEach(function (el) {
        el.addEventListener('click', function () { if (window.innerWidth < 640) closeSidebar(); });
    });

    var userToggle = document.getElementById('sidebarUserToggle');
    var userMenu   = document.getElementById('sidebarUserMenu');
    userToggle?.addEventListener('click', function () {
        var open = userMenu?.classList.toggle('open');
        userToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    // Password visibility (profile page)
    var toggleBtn = document.getElementById('togglePassword');
    if (toggleBtn) {
        var pw      = document.getElementById('password');
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

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script src="{{ asset('css/public.js') }}?v={{ filemtime(public_path('css/public.js')) }}"></script>
@yield('js')

<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js', { scope: '/' })
        .catch(function(err) { console.warn('[SW] Registration failed:', err); });
}
</script>

</body>
</html>
