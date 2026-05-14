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
    <meta name="description" content="Sistem Informasi Penugasan dan Pelaporan Teknisi">

    <title>{{ config('app.name', 'SIPRT') }}</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Tabler Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    @yield('css')
</head>
<body class="pwa-body">

@auth
{{-- ════════════════════════════════════════════════════════════
     AUTHENTICATED LAYOUT
     Desktop : fixed sidebar (220px) + topbar + scrollable content
     Mobile  : topbar + full-width content + bottom nav
     ════════════════════════════════════════════════════════════ --}}

    {{-- ── Sidebar (tablet/desktop only) ── --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <img src="{{ asset('favicon/android-chrome-192x192.png') }}" alt="Logo" style="width:24px;height:24px">
            </div>
            <span>SIPRT</span>
        </div>

        <nav class="sidebar-nav">
            @canany(['view users', 'create customers', 'edit customers'])
            <a href="{{ route('dashboard.sales') }}"
               class="nav-item {{ request()->routeIs('dashboard.sales') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard"></i><span>Dashboard Sales</span>
            </a>
            @endcanany

            <a href="{{ route('dashboard.teknisi') }}"
               class="nav-item {{ request()->routeIs('dashboard.teknisi') ? 'active' : '' }}">
                <i class="ti ti-tool"></i><span>Dashboard Teknisi</span>
            </a>

            @canany(['view users', 'create customers'])
            <a href="{{ route('tugas.index') }}"
               class="nav-item {{ request()->routeIs('tugas.*') ? 'active' : '' }}">
                <i class="ti ti-clipboard-list"></i><span>Tugas</span>
            </a>
            @endcanany

            <a href="{{ route('laporan.index') }}"
               class="nav-item {{ request()->routeIs('laporan.*') && !request()->routeIs('customers.laporan') ? 'active' : '' }}">
                <i class="ti ti-file-text"></i><span>Laporan</span>
            </a>

            @can('view customers')
            <a href="{{ route('customers.index') }}"
               class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <i class="ti ti-users"></i><span>Customer</span>
            </a>
            @endcan

            @can('view users')
            <a href="{{ route('template.index') }}"
               class="nav-item {{ request()->routeIs('template.*') ? 'active' : '' }}">
                <i class="ti ti-template"></i><span>Custom Template</span>
            </a>
            <a href="{{ route('users.index') }}"
               class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="ti ti-user-cog"></i><span>Manage User</span>
            </a>
            @endcan
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                    <div class="sidebar-user-role">{{ Auth::user()->roles->first()?->name ?? 'user' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="sidebar-logout">
                    <i class="ti ti-logout"></i><span>Keluar</span>
                </button>
            </form>
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
                {{-- Notification bell (teknisi only) --}}
                @if(Auth::user()->hasRole('teknisi'))
                <button class="notif-bell-btn" id="notifBell" title="Notifikasi">
                    <i class="ti ti-bell"></i>
                    <span class="notif-badge" id="notifBadge"></span>
                </button>
                @endif
                <span class="pwa-topbar-user d-none d-md-block">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="mb-0 d-none d-md-block">
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

        @canany(['view users', 'create customers', 'edit customers'])
        <a href="{{ route('dashboard.sales') }}"
           class="pwa-bn-item {{ request()->routeIs('dashboard.sales') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard"></i>
            <span>Sales</span>
        </a>
        @endcanany

        @if(Auth::user()->hasRole('teknisi'))
        <a href="{{ route('dashboard.teknisi') }}"
           class="pwa-bn-item {{ request()->routeIs('dashboard.teknisi') ? 'active' : '' }}">
            <i class="ti ti-tool"></i>
            <span>Dashboard</span>
        </a>
        @endif

        @canany(['view users', 'create customers'])
        <a href="{{ route('tugas.index') }}"
           class="pwa-bn-item {{ request()->routeIs('tugas.*') ? 'active' : '' }}">
            <i class="ti ti-clipboard-list"></i>
            <span>Tugas</span>
        </a>
        @endcanany

        <a href="{{ route('laporan.index') }}"
           class="pwa-bn-item {{ request()->routeIs('laporan.*') && !request()->routeIs('customers.laporan') ? 'active' : '' }}">
            <i class="ti ti-file-text"></i>
            <span>Laporan</span>
        </a>

        @can('view customers')
        <a href="{{ route('customers.index') }}"
           class="pwa-bn-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <i class="ti ti-users"></i>
            <span>Customer</span>
        </a>
        @endcan

        @can('view users')
        <a href="{{ route('users.index') }}"
           class="pwa-bn-item {{ request()->routeIs('users.*') || request()->routeIs('template.*') ? 'active' : '' }}">
            <i class="ti ti-user-cog"></i>
            <span>Users</span>
        </a>
        @endcan

    </nav>

@else
{{-- ════════════════════════════════════════════════════════════
     GUEST LAYOUT — login / register (no sidebar, no nav)
     ════════════════════════════════════════════════════════════ --}}
<main class="pwa-guest-main">
    @yield('content')
</main>
@endauth

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmF23EStqFUkBmZG1t5K3DNZX1ub"
        crossorigin="anonymous"></script>

<script>
(function () {
    // ── Sidebar toggle (mobile) ──
    const menuBtn  = document.getElementById('menuToggle');
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');

    function openSidebar()  { sidebar?.classList.add('open'); overlay?.classList.add('show'); }
    function closeSidebar() { sidebar?.classList.remove('open'); overlay?.classList.remove('show'); }

    menuBtn?.addEventListener('click', () =>
        sidebar?.classList.contains('open') ? closeSidebar() : openSidebar());
    overlay?.addEventListener('click', closeSidebar);

    // Close sidebar on nav link tap (mobile)
    sidebar?.querySelectorAll('.nav-item').forEach(el =>
        el.addEventListener('click', () => { if (window.innerWidth < 640) closeSidebar(); }));

    // ── Password visibility (login page) ──
    const toggleBtn = document.getElementById('togglePassword');
    if (toggleBtn) {
        const pw      = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        toggleBtn.addEventListener('click', () => {
            const hidden = pw.type === 'password';
            pw.type = hidden ? 'text' : 'password';
            eyeIcon.classList.toggle('bi-eye', !hidden);
            eyeIcon.classList.toggle('bi-eye-slash', hidden);
        });
    }
})();
</script>

{{-- jQuery + DataTables --}}
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script src="{{ asset('css/public.js') }}?v={{ filemtime(public_path('css/public.js')) }}"></script>
@yield('js')

{{-- ── Pusher / Laravel Echo (teknisi only, runs when Pusher key is configured) ── --}}
@auth
@if(Auth::user()->hasRole('teknisi') && config('broadcasting.connections.pusher.key'))
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.17.1/dist/echo.iife.js"></script>
<script>
(function () {
    /* Notification toast container */
    var $toastBox = $('<div id="notifToastBox" style="position:fixed;top:68px;right:12px;z-index:9998;display:flex;flex-direction:column;gap:8px;max-width:320px;"></div>').appendTo('body');

    function showToast(data) {
        var id = 'nt' + Date.now();
        var html = '<div id="' + id + '" class="notif-toast" style="cursor:pointer;" onclick="window.location=\'' + data.url + '\'">'
            + '<div class="notif-toast-header">'
            + '<i class="ti ti-clipboard-list me-1" style="color:var(--blue);"></i>'
            + '<strong style="flex:1;font-size:.8rem;">Tugas Baru</strong>'
            + '<button style="background:none;border:none;cursor:pointer;color:var(--text-secondary);font-size:16px;line-height:1;" '
            + 'onclick="event.stopPropagation();$(\'#' + id + '\').remove();">&times;</button>'
            + '</div>'
            + '<div style="padding:.5rem .75rem;">'
            + '<div style="font-size:.82rem;font-weight:600;color:var(--text);">' + $('<span>').text(data.title).html() + '</div>'
            + '<div style="font-size:.72rem;color:var(--text-secondary);margin-top:2px;">'
            + $('<span>').text(data.customer_name).html() + ' · ' + $('<span>').text(data.due_date).html()
            + '</div>'
            + '</div>'
            + '</div>';
        $toastBox.prepend(html);
        setTimeout(function() { $('#' + id).fadeOut(400, function() { $(this).remove(); }); }, 7000);
    }

    function bumpBadge() {
        var $b = $('#notifBadge');
        var n = (parseInt($b.text()) || 0) + 1;
        $b.text(n).addClass('has-notif');
    }

    /* Clear badge on bell click */
    $('#notifBell').on('click', function() {
        $('#notifBadge').text('').removeClass('has-notif');
    });

    /* Connect Echo */
    try {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config("broadcasting.connections.pusher.key") }}',
            cluster: '{{ config("broadcasting.connections.pusher.options.cluster", "ap1") }}',
            forceTLS: true,
            authEndpoint: '/broadcasting/auth',
            auth: { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } },
        });

        Echo.private('App.Models.User.{{ Auth::id() }}')
            .listen('.TaskAssigned', function(data) {
                showToast(data);
                bumpBadge();
            });
    } catch (err) {
        console.warn('[SIPRT] Echo not connected:', err.message);
    }
})();
</script>
@endif

{{-- ── Service Worker registration ── --}}
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js', { scope: '/' })
        .catch(function(err) { console.warn('[SW] Registration failed:', err); });
}
</script>
@endauth

</body>
</html>
