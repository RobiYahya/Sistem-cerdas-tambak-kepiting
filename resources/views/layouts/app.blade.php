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
    <link rel="stylesheet" href="{{ asset('css/aquacrab.css') }}?v={{ @filemtime(public_path('css/aquacrab.css')) ?: time() }}">
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
                    <div class="brand-title-wrap">
                        <span class="brand-name">AquaCrab</span>
                        <span class="brand-ver-badge">v2.4</span>
                    </div>
                    <div class="brand-sub">IOT MONITORING SYSTEM</div>
                </div>
            </div>

            {{-- Kartu Profil Pengguna di Sidebar (RBAC: Nelayan vs Teknisi) --}}
            @php
                $activeRole = session('user_role', 'teknisi');
                $isTeknisi = ($activeRole === 'teknisi');
                $userName = $isTeknisi ? 'operator_tambak' : 'pak_robi_nelayan';
                $userEmail = $isTeknisi ? 'budi.santoso@aquacrab.io..' : 'robi.petambak@aquacrab.io..';
                $userAvatar = $isTeknisi ? 'TK' : 'NL';
                $roleLabel = $isTeknisi ? 'TEKNISI' : 'NELAYAN';
            @endphp
            <div class="sidebar-user-card">
                <div class="suc-top">
                    <div class="suc-avatar">{{ $userAvatar }}</div>
                    <div class="suc-info">
                        <strong class="suc-name">{{ $userName }}</strong>
                        <span class="suc-email" title="{{ $isTeknisi ? 'budi.santoso@aquacrab.io' : 'robi.petambak@aquacrab.io' }}">{{ $userEmail }}</span>
                    </div>
                </div>
                <div class="suc-bottom">
                    <span class="suc-role-pill"><i class="dot"></i> {{ $roleLabel }}</span>
                    <a href="{{ route('switch-role') }}" class="suc-switch-role" title="Beralih peran: Nelayan / Teknisi" style="text-decoration: none;">
                        <i class="bi bi-arrow-repeat"></i> Ganti Peran
                    </a>
                </div>
            </div>

            {{-- Navigasi Menu Sidebar: DAFTAR PERANGKAT --}}
            <div class="sidebar-nav-group">
                <span class="sidebar-nav-heading">DAFTAR PERANGKAT</span>
                <nav class="menu">
                    @foreach ($menus as $menu)
                        @if (($menu['route'] ?? '') !== 'logout' && ($menu['label'] ?? '') !== 'Logout')
                            @php
                                $url = $menu['url'] ?? (isset($menu['route']) ? route($menu['route']) : '#');
                                $isActive = false;
                                if (isset($menu['active'])) {
                                    $isActive = $menu['active'];
                                } elseif (isset($menu['route'])) {
                                    $isActive = request()->routeIs($menu['route']);
                                }
                            @endphp
                            <a href="{{ $url }}" class="menu-item {{ $isActive ? 'active' : '' }}">
                                <i class="bi {{ $menu['icon'] }}"></i>
                                <span>{{ $menu['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>

            {{-- Navigasi Menu Sidebar: AKUN PENGGUNA --}}
            <div class="sidebar-nav-group">
                <span class="sidebar-nav-heading">AKUN PENGGUNA</span>
                <nav class="menu">
                    <a href="{{ route('dashboard') }}" class="menu-item">
                        <i class="bi bi-person"></i>
                        <span>Profil Petambak</span>
                    </a>
                </nav>
            </div>
        </div>

        <div class="sidebar-bottom">
            <div class="sidebar-iot-telemetry">
                <div class="sit-head">
                    <span class="sit-title">Telemetri IoT Live</span>
                    <span class="sit-badge">LIVE</span>
                </div>
                <div class="sit-meta">MQTT &bull; TLS 1.3 &bull; 24ms</div>
            </div>

            <a href="{{ route('logout') }}" class="sidebar-logout-link" title="Keluar Sesi">
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar Sesi</span>
            </a>
        </div>
    </aside>

    {{-- ========== TOP BAR MOBILE (HANYA LAYAR HP <= 820px) ========== --}}
    <header class="mobile-top-bar">
        <div class="mobile-top-brand">
            <div class="brand-logo-mini">A</div>
            <div>
                <span class="mobile-brand-title">AquaCrab</span>
                <span class="mobile-brand-sub">IoT Monitoring</span>
            </div>
        </div>
        <div class="mobile-top-actions">
            <span class="mobile-status-dot">
                <i class="dot"></i> Online
            </span>
            <a href="{{ route('logout') }}" class="mobile-top-logout" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </header>

    {{-- ========== KONTEN ========== --}}
    <main class="content">
        @if (session('pesan_warning'))
            <div style="background: #fef2f2; border: 1.5px solid #fecaca; color: #b91c1c; padding: 12px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 13.5px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                <i class="bi bi-exclamation-circle-fill" style="font-size: 16px;"></i>
                <span>{{ session('pesan_warning') }}</span>
            </div>
        @endif

        @if (session('pesan_sukses'))
            <div style="background: #f0fdf4; border: 1.5px solid #bbf7d0; color: #15803d; padding: 12px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 13.5px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                <i class="bi bi-check-circle-fill" style="font-size: 16px;"></i>
                <span>{{ session('pesan_sukses') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ========== BOTTOM NAV (KHUSUS LAYAR HP / PWA) ========== --}}
    <nav class="mobile-nav">
        @php
            $shortLabels = [
                'Perangkat Saya'   => 'Perangkat',
                'Dashboard'        => 'Monitoring',
                'Riwayat Sensor'   => 'Riwayat',
                'Inferensi Fuzzy'  => 'Fuzzy',
                'Kontrol Aktuator' => 'Aktuator',
                'Logout'           => 'Keluar',
            ];
        @endphp

        @foreach ($menus as $menu)
            @php
                $url = $menu['url'] ?? (isset($menu['route']) ? route($menu['route']) : '#');
                $isActive = false;
                if (isset($menu['active'])) {
                    $isActive = $menu['active'];
                } elseif (isset($menu['route'])) {
                    $isActive = request()->routeIs($menu['route']);
                }
                $label = $shortLabels[$menu['label']] ?? $menu['label'];
            @endphp
            <a href="{{ $url }}" class="mobile-nav-item {{ $isActive ? 'active' : '' }}">
                <i class="bi {{ $menu['icon'] }}"></i>
                <span>{{ $label }}</span>
            </a>
        @endforeach
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>