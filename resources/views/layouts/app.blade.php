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

    <link rel="stylesheet" href="{{ asset('css/public.css') }}?v={{ filemtime(public_path('css/public.css')) }}">
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
            {{-- Admin / Sales / Teknisi --}}
            @canany(['view users', 'create customers', 'edit customers'])
            <a href="{{ route('dashboard.sales') }}"
               class="nav-item {{ request()->routeIs('dashboard.sales') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard"></i><span>Dashboard Sales</span>
            </a>
            <a href="{{ route('dashboard.teknisi.all') }}"
               class="nav-item {{ request()->routeIs('dashboard.teknisi.all') ? 'active' : '' }}">
                <i class="ti ti-tool"></i><span>Dashboard Teknisi</span>
            </a>
            @endcanany

            @if(Auth::user()->hasRole('teknisi'))
            <a href="{{ route('dashboard.teknisi.my') }}"
               class="nav-item {{ request()->routeIs('dashboard.teknisi.my') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard"></i><span>Dashboard</span>
            </a>
            @endif

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
            <button class="sidebar-user-toggle" id="sidebarUserToggle" aria-expanded="false">
                <div class="sidebar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                    <div class="sidebar-user-role">{{ Auth::user()->roles->first()?->name ?? 'user' }}</div>
                </div>
                <i class="ti ti-chevron-up sidebar-user-chevron"></i>
            </button>
            <div class="sidebar-user-menu" id="sidebarUserMenu">
                <a href="{{ route('profile.signature.show') }}" class="sidebar-user-menu-item">
                    <i class="ti ti-writing"></i><span>Tanda Tangan</span>
                </a>
                <a href="{{ route('profile.password.show') }}" class="sidebar-user-menu-item">
                    <i class="ti ti-lock"></i><span>Edit Password</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mb-0">
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
                {{-- Notification bell (teknisi, admin, sales) --}}
                @if(Auth::user()->hasAnyRole(['teknisi', 'admin', 'sales']))
                <div class="notif-wrap" id="notifWrap">
                    <button class="notif-bell-btn" id="notifBell" title="Notifikasi" aria-expanded="false">
                        <i class="ti ti-bell"></i>
                        @php $unreadCount = Auth::user()->unreadNotifications()->count(); @endphp
                        <span class="notif-badge{{ $unreadCount ? ' has-notif' : '' }}" id="notifBadge">
                            {{ $unreadCount ?: '' }}
                        </span>
                    </button>
                </div>
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

        @if(Auth::user()->hasRole('teknisi'))
        {{-- Teknisi: Beranda | Tugas | Laporan | Keluar --}}
        <a href="{{ route('dashboard.teknisi.my') }}"
           class="pwa-bn-item {{ request()->routeIs('dashboard.teknisi.my') ? 'active' : '' }}">
            <i class="ti ti-home"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('tugas.index') }}"
           class="pwa-bn-item {{ request()->routeIs('tugas.*') ? 'active' : '' }}">
            <i class="ti ti-clipboard-list"></i>
            <span>Tugas</span>
        </a>
        <a href="{{ route('laporan.index') }}"
           class="pwa-bn-item {{ request()->routeIs('laporan.*') && !request()->routeIs('customers.laporan') ? 'active' : '' }}">
            <i class="ti ti-file-text"></i>
            <span>Laporan</span>
        </a>
        <button type="button" class="pwa-bn-item"
                onclick="document.getElementById('tekBnLogout').submit()"
                style="background:none;border:none;width:100%;cursor:pointer;">
            <i class="ti ti-logout"></i>
            <span>Keluar</span>
        </button>
        <form id="tekBnLogout" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>

        @else
        {{-- Admin / Sales --}}
        @canany(['view users', 'create customers', 'edit customers'])
        <a href="{{ route('dashboard.sales') }}"
           class="pwa-bn-item {{ request()->routeIs('dashboard.sales') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard"></i>
            <span>Sales</span>
        </a>
        @endcanany

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
        @endif

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

    // ── Sidebar user menu toggle ──
    const userToggle = document.getElementById('sidebarUserToggle');
    const userMenu   = document.getElementById('sidebarUserMenu');
    userToggle?.addEventListener('click', () => {
        const open = userMenu?.classList.toggle('open');
        userToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

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

{{-- ── Notification bell (teknisi, admin, sales) ── --}}
@auth
@if(Auth::user()->hasAnyRole(['teknisi', 'admin', 'sales']))
@if(config('broadcasting.connections.pusher.key'))
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.17.1/dist/echo.iife.js"></script>
@endif
<script>
(function () {
    var CSRF = '{{ csrf_token() }}';
    var drawerOpen = false;

    /* ── Toast ── */
    var $toastBox = $('<div id="notifToastBox" style="position:fixed;top:68px;right:12px;z-index:10000;display:flex;flex-direction:column;gap:8px;max-width:320px;"></div>').appendTo('body');

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
            + '</div></div></div>';
        $toastBox.prepend(html);
        setTimeout(function() { $('#' + id).fadeOut(400, function() { $(this).remove(); }); }, 7000);
    }

    /* ── Badge ── */
    function setBadge(n) {
        var $b = $('#notifBadge');
        if (n > 0) { $b.text(n).addClass('has-notif'); }
        else        { $b.text('').removeClass('has-notif'); }
    }

    function bumpBadge() {
        setBadge((parseInt($('#notifBadge').text()) || 0) + 1);
    }

    /* ── Backdrop ── */
    var $backdrop = $('<div id="notifBackdrop"></div>').appendTo('body');

    /* ── Drawer (appended to body, slides from topbar) ── */
    var $drawer = $(
        '<div id="notifDrawer">'
      + '<div style="background:#0D47A1;padding:10px 16px 14px;">'
      + '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2px;">'
      + '<span style="font-size:15px;font-weight:500;color:#fff;">Notifikasi</span>'
      + '<div style="display:flex;align-items:center;gap:12px;">'
      + '<span id="notifReadAll" style="font-size:11px;color:#90CAF9;cursor:pointer;text-decoration:underline;">Tandai semua dibaca</span>'
      + '<button id="notifClose" style="cursor:pointer;width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.15);border:none;display:flex;align-items:center;justify-content:center;">'
      + '<i class="ti ti-x" style="font-size:15px;color:#fff;"></i></button>'
      + '</div></div>'
      + '<span id="notifCountLabel" style="font-size:11px;color:#90CAF9;">Memuat...</span>'
      + '</div>'
      + '<div id="notifList" style="max-height:340px;overflow-y:auto;">'
      + '<div id="notifEmpty" style="padding:2rem 1rem;text-align:center;color:#7a8099;font-size:13px;line-height:1.8;">'
      + '<i class="ti ti-bell-off" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.3;"></i>Tidak ada notifikasi</div>'
      + '</div>'
      + '<div style="padding:10px 14px 14px;text-align:center;border-top:0.5px solid #E3F2FD;">'
      + '<a href="{{ route("laporan.index") }}" style="font-size:12px;color:#1565C0;text-decoration:none;">Lihat semua notifikasi ↗</a>'
      + '</div>'
      + '</div>'
    ).appendTo('body');

    /* ── Build notification item matching reference design ── */
    function buildItem(n) {
        var d        = n.data;
        var isUnread = !n.read;
        var bg       = isUnread ? '#F0F7FF' : '#fff';
        var titleC   = isUnread ? '#0C447C' : '#374151';
        var subC     = isUnread ? '#1565C0' : '#7a8099';
        var dotHtml  = isUnread
            ? '<div style="position:absolute;top:0;right:0;width:9px;height:9px;background:#FF6B35;border-radius:50%;border:1.5px solid #fff;"></div>'
            : '';

        var icon, label, sub;
        if (d.type === 'task_started') {
            icon  = 'ti-player-play';
            label = 'Tugas Dimulai';
            sub   = $('<span>').text(d.title).html() + ' &middot; ' + $('<span>').text(d.teknisi_name).html();
        } else if (d.type === 'task_completed') {
            icon  = 'ti-file-check';
            label = 'Laporan Dikirim';
            sub   = $('<span>').text(d.title).html() + ' &middot; ' + $('<span>').text(d.teknisi_name).html();
        } else {
            icon  = 'ti-clipboard-plus';
            label = 'Tugas Baru Ditugaskan';
            sub   = $('<span>').text(d.title).html() + ' &middot; ' + $('<span>').text(d.customer_name).html();
        }

        return '<a href="' + d.url + '" class="notif-item' + (isUnread ? ' unread' : '') + '" data-id="' + n.id + '" style="display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border-bottom:0.5px solid #E3F2FD;text-decoration:none;background:' + bg + ';cursor:pointer;">'
            + '<div style="width:36px;height:36px;border-radius:50%;background:#E3F2FD;display:flex;align-items:center;justify-content:center;flex-shrink:0;position:relative;">'
            + '<i class="ti ' + icon + '" style="font-size:18px;color:#1565C0;"></i>'
            + dotHtml + '</div>'
            + '<div style="flex:1;min-width:0;">'
            + '<div style="font-size:12px;font-weight:500;color:' + titleC + ';margin-bottom:2px;">' + label + '</div>'
            + '<div style="font-size:11px;color:' + subC + ';line-height:1.5;">' + sub + '</div>'
            + '<div style="font-size:10px;color:#7986CB;margin-top:4px;display:flex;align-items:center;gap:4px;">'
            + '<i class="ti ti-clock" style="font-size:11px;"></i> ' + n.time + '</div>'
            + '</div></a>';
    }

    /* ── Load notifications ── */
    function loadNotifications() {
        $.get('/notifications', function(res) {
            $('#notifList').find('.notif-item').remove();
            if (res.notifications.length === 0) {
                $('#notifEmpty').show();
            } else {
                $('#notifEmpty').hide();
                $('#notifList').append(res.notifications.map(buildItem).join(''));
            }
            setBadge(res.unread);
            var label = res.unread > 0
                ? res.unread + ' notifikasi belum dibaca'
                : 'Semua notifikasi sudah dibaca';
            $('#notifCountLabel').text(label);
            /* Dim "read all" when nothing unread */
            $('#notifReadAll').css({ opacity: res.unread > 0 ? '1' : '.4', pointerEvents: res.unread > 0 ? 'auto' : 'none' });
        });
    }

    /* ── Open / close ── */
    function openDrawer() {
        loadNotifications();
        $backdrop.addClass('open');
        $drawer.addClass('open');
        $('#notifBell').attr('aria-expanded', 'true');
        drawerOpen = true;
    }

    function closeDrawer() {
        $backdrop.removeClass('open');
        $drawer.removeClass('open');
        $('#notifBell').attr('aria-expanded', 'false');
        drawerOpen = false;
    }

    $('#notifBell').on('click', function(e) {
        e.stopPropagation();
        drawerOpen ? closeDrawer() : openDrawer();
    });

    $backdrop.on('click', closeDrawer);

    $(document).on('click', '#notifClose', closeDrawer);

    /* ── Mark single read then navigate ── */
    $(document).on('click', '.notif-item', function(e) {
        e.preventDefault();
        var id  = $(this).data('id');
        var url = $(this).attr('href');
        $.post('/notifications/' + id + '/read', { _token: CSRF }, function() {
            window.location = url;
        });
    });

    /* ── Mark all read ── */
    $(document).on('click', '#notifReadAll', function(e) {
        e.stopPropagation();
        $.post('/notifications/read-all', { _token: CSRF }, function() {
            loadNotifications();
        });
    });

    @if(config('broadcasting.connections.pusher.key'))
    /* ── Real-time Echo ── */
    try {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config("broadcasting.connections.pusher.key") }}',
            cluster: '{{ config("broadcasting.connections.pusher.options.cluster", "ap1") }}',
            forceTLS: true,
            authEndpoint: '/broadcasting/auth',
            auth: { headers: { 'X-CSRF-TOKEN': CSRF } },
        });

        Echo.private('App.Models.User.{{ Auth::id() }}')
            .listen('.TaskAssigned', function(data) {
                showToast(data);
                bumpBadge();
                if (drawerOpen) loadNotifications();
            });
    } catch (err) {
        console.warn('[SIPRT] Echo not connected:', err.message);
    }
    @endif
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
