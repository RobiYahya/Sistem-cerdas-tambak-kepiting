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

            @if ($selectedDevice ?? null)
                {{-- MODE MONITORING WADAH TERPILIH (Sesuai Mockup Gambar media_1794371967886_c2c1a0e9.png) --}}
                <div class="sidebar-device-box">
                    <a href="{{ route('dashboard') }}" class="btn-sidebar-back" title="Kembali ke Daftar Perangkat">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali ke Perangkat</span>
                    </a>

                    <div class="sidebar-device-divider"></div>

                    <div class="sadc-top">
                        <span class="sadc-lbl">Wadah Aktif:</span>
                        <span class="sadc-status-online"><i class="dot"></i> ONLINE</span>
                    </div>

                    {{-- 1 Card Pemilih Wadah --}}
                    <div class="sidebar-active-device-card" title="Klik untuk beralih jerigen budidaya">
                        <div class="sadc-name-row">
                            <strong class="sadc-name">{{ Str::limit($selectedDevice['nama'], 20) }}</strong>
                            <i class="bi bi-chevron-down sadc-chevron"></i>
                        </div>
                        <div class="sadc-meta-row">
                            <span class="sadc-code">{{ $selectedDevice['id'] }}</span>
                            <span class="sadc-dot">&bull;</span>
                            <span class="sadc-skor">Skor {{ number_format(floatval(str_replace(',', '.', $selectedDevice['indeks'] ?? 75.3)), 1, ',', '.') }}</span>
                        </div>
                        <select class="sadc-select-overlay" onchange="window.location.href='{{ route('dashboard') }}?device=' + this.value" title="Pilih Jerigen Budidaya">
                            @foreach ($devices ?? [] as $dev)
                                <option value="{{ $dev['id'] }}" {{ $dev['id'] === $selectedDevice['id'] ? 'selected' : '' }}>
                                    {{ $dev['nama'] }} ({{ $dev['id'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Group 1: MONITORING WADAH --}}
                <div class="sidebar-nav-group">
                    <span class="sidebar-nav-heading">MONITORING WADAH</span>
                    <nav class="menu">
                        <a href="{{ route('dashboard', ['device' => $selectedDevice['id']]) }}" class="menu-item active">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard Wadah</span>
                        </a>
                        <a href="{{ route('inferensi-fuzzy') }}" class="menu-item">
                            <i class="bi bi-water"></i>
                            <span>Analisis Kualitas Air</span>
                        </a>
                        <a href="{{ route('riwayat-sensor') }}" class="menu-item">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>Riwayat & Laporan</span>
                        </a>
                        <a href="#" class="menu-item menu-item-badge">
                            <i class="bi bi-bell"></i>
                            <span>Notifikasi Wadah</span>
                            <span class="menu-badge-red">2</span>
                        </a>
                    </nav>
                </div>

                {{-- Group 2 & 3: KHUSUS TEKNISI (KENDALI & KONFIGURASI) --}}
                @if ($isTeknisi)
                    <div class="sidebar-nav-group">
                        <span class="sidebar-nav-heading">KENDALI & INFERENSI</span>
                        <nav class="menu">
                            <a href="{{ route('kontrol-aktuator') }}" class="menu-item">
                                <i class="bi bi-sliders"></i>
                                <span>Kontrol Aktuator</span>
                            </a>
                            <a href="{{ route('inferensi-fuzzy') }}" class="menu-item">
                                <i class="bi bi-bezier2"></i>
                                <span>Analisis Fuzzy Detail</span>
                            </a>
                        </nav>
                    </div>

                    <div class="sidebar-nav-group">
                        <span class="sidebar-nav-heading">KONFIGURASI & KALIBRASI</span>
                        <nav class="menu">
                            <a href="{{ route('kalibrasi-sensor') }}" class="menu-item">
                                <i class="bi bi-bullseye"></i>
                                <span>Kalibrasi Sensor</span>
                            </a>
                            <a href="{{ route('pengaturan') }}" class="menu-item">
                                <i class="bi bi-gear"></i>
                                <span>Konfigurasi Fuzzy</span>
                            </a>
                            <a href="#" class="menu-item">
                                <i class="bi bi-cpu"></i>
                                <span>Diagnostik Hardware</span>
                            </a>
                        </nav>
                    </div>
                @endif

            @else
                {{-- MODE GLOBAL: PERANGKAT SAYA (Sebelum Memilih Perangkat) --}}
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

                <div class="sidebar-nav-group">
                    <span class="sidebar-nav-heading">DAFTAR PERANGKAT</span>
                    <nav class="menu">
                        <a href="{{ route('dashboard') }}" class="menu-item active">
                            <i class="bi bi-hdd-network"></i>
                            <span>Perangkat Saya</span>
                        </a>
                    </nav>
                </div>

                <div class="sidebar-nav-group">
                    <span class="sidebar-nav-heading">AKUN PENGGUNA</span>
                    <nav class="menu">
                        <a href="{{ route('dashboard') }}" class="menu-item">
                            <i class="bi bi-person"></i>
                            <span>Profil Petambak</span>
                        </a>
                    </nav>
                </div>
            @endif
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
                    <div class="brand-name">AquaCrab</div>
                    <div class="brand-sub">Water Monitoring System</div>
                </div>
            </div>

            <nav class="menu">
                @foreach ($menus as $menu)
                    @if ($menu['route'] !== 'logout')
                        <a href="{{ route($menu['route']) }}"
                           class="menu-item {{ request()->routeIs($menu['route']) ? 'active' : '' }}">
                            @if (!empty($menu['svg']))
                                {!! $menu['svg'] !!}
                            @else
                                <i class="bi {{ $menu['icon'] }}"></i>
                            @endif
                            <span>{{ $menu['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>
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
        <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('riwayat-sensor') }}" class="mobile-nav-item {{ request()->routeIs('riwayat-sensor') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span>Riwayat</span>
        </a>
        <a href="{{ route('inferensi-fuzzy') }}" class="mobile-nav-item {{ request()->routeIs('inferensi-fuzzy') ? 'active' : '' }}">
            <i class="bi bi-circle-half"></i>
            <span>Fuzzy</span>
        </a>
        <a href="{{ route('kontrol-aktuator') }}" class="mobile-nav-item {{ request()->routeIs('kontrol-aktuator') ? 'active' : '' }}">
            <i class="bi bi-sliders"></i>
            <span>Aktuator</span>
        </a>
        {{-- Di-comment karena belum memiliki halaman fisik: --}}
        {{-- <a href="{{ route('notifikasi-alarm') }}" class="mobile-nav-item {{ request()->routeIs('notifikasi-alarm') ? 'active' : '' }}">
            <i class="bi bi-alarm"></i>
            <span>Notifikasi</span>
        </a> --}}
        {{-- <a href="{{ route('pengaturan') }}" class="mobile-nav-item {{ request()->routeIs('pengaturan') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            <span>Pengaturan</span>
        </a> --}}
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const trigger = document.getElementById('sidebarDeviceTrigger');
    const dropdown = document.getElementById('sidebarDeviceDropdown');
    if (trigger && dropdown) {
        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = dropdown.classList.contains('open');
            if (isOpen) {
                dropdown.classList.remove('open');
                trigger.classList.remove('open');
            } else {
                dropdown.classList.add('open');
                trigger.classList.add('open');
            }
        });

        document.addEventListener('click', function (e) {
            if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
                trigger.classList.remove('open');
            }
        });
    }
});
</script>
@stack('scripts')
</body>
</html>