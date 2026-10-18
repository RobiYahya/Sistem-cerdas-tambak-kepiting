<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'AquaCrab') - IoT Monitoring System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/aquacrab.css') }}">
    @stack('styles')
</head>
<body>
<div class="app">

    {{-- ========== SIDEBAR ========== --}}
    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="brand">
                <div class="brand-logo">A</div>
                <div>
                    <div class="brand-name">AquaCrab</div>
                    <div class="brand-sub">IoT Monitoring System</div>
                </div>
            </div>

            <nav class="menu">
                @foreach ($menus as $menu)
                    @if ($menu['route'] !== 'logout')
                        <a href="{{ route($menu['route']) }}"
                           class="menu-item {{ request()->routeIs($menu['route']) ? 'active' : '' }}">
                            <i class="bi {{ $menu['icon'] }}"></i>
                            <span>{{ $menu['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>

        <div class="sidebar-bottom">
            <a href="{{ route('logout') }}" class="menu-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    {{-- ========== KONTEN ========== --}}
    <main class="content">
        @yield('content')
    </main>

    {{-- ========== BOTTOM NAV (KHUSUS LAYAR HP / PWA) ========== --}}
    <nav class="mobile-nav">
        <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('riwayat-sensor') }}" class="mobile-nav-item {{ request()->routeIs('riwayat-sensor') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span>Riwayat</span>
        </a>
        <a href="{{ route('kontrol-aktuator') }}" class="mobile-nav-item {{ request()->routeIs('kontrol-aktuator') ? 'active' : '' }}">
            <i class="bi bi-sliders"></i>
            <span>Aktuator</span>
        </a>
        <a href="{{ route('notifikasi-alarm') }}" class="mobile-nav-item {{ request()->routeIs('notifikasi-alarm') ? 'active' : '' }}">
            <i class="bi bi-alarm"></i>
            <span>Notifikasi</span>
        </a>
        <a href="{{ route('pengaturan') }}" class="mobile-nav-item {{ request()->routeIs('pengaturan') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            <span>Pengaturan</span>
        </a>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>