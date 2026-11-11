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
                        <a href="{{ route('dashboard', ['device' => $selectedDevice['id']]) }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard Wadah</span>
                        </a>
                        <a href="{{ route('inferensi-fuzzy') }}" class="menu-item {{ request()->routeIs('inferensi-fuzzy') ? 'active' : '' }}">
                            <i class="bi bi-water"></i>
                            <span>Analisis Kualitas Air</span>
                        </a>
                        <a href="{{ route('riwayat-sensor') }}" class="menu-item {{ request()->routeIs('riwayat-sensor') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>Riwayat & Laporan</span>
                        </a>
                        <a href="{{ route('notifikasi-wadah') }}" class="menu-item menu-item-badge {{ request()->routeIs('notifikasi-wadah') ? 'active' : '' }}">
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

                {{-- Group: AKUN PENGGUNA (Profil Saya) --}}
                <div class="sidebar-nav-group">
                    <span class="sidebar-nav-heading">AKUN PENGGUNA</span>
                    <nav class="menu">
                        <a href="{{ route('dashboard') }}" class="menu-item">
                            <i class="bi bi-person"></i>
                            <span>Profil Saya</span>
                        </a>
                    </nav>
                </div>

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

    {{-- ========== NOTIFICATION DRAWER (Panel Kanan, Semua Alat) ========== --}}
    <div class="notif-drawer-overlay" id="notifDrawerOverlay"></div>
    <aside class="notif-drawer" id="notifDrawer" role="dialog" aria-label="Notifikasi & Peringatan">

        {{-- Header Drawer --}}
        <div class="nd-header">
            <div class="nd-header-left">
                <i class="bi bi-bell nd-bell-icon"></i>
                <div>
                    <h3 class="nd-title">Notifikasi &amp; Peringatan</h3>
                    <p class="nd-subtitle" id="ndSubtitle">2 belum dikonfirmasi</p>
                </div>
            </div>
            <button class="nd-close-btn" id="notifDrawerClose" title="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Tab Filter --}}
        <div class="nd-tabs">
            <button class="nd-tab active" data-filter="all">Semua</button>
            <button class="nd-tab" data-filter="critical">Critical</button>
            <button class="nd-tab" data-filter="high">High</button>
            <button class="nd-tab" data-filter="medium">Medium</button>
            <div class="nd-tab-spacer"></div>
            <button class="nd-mark-all" id="ndMarkAll" title="Tandai semua selesai">
                <i class="bi bi-check2-all"></i> Tandai Selesai
            </button>
        </div>

        {{-- Daftar Notifikasi --}}
        <div class="nd-list" id="ndList">

            {{-- Item: Kritis --}}
            <article class="nd-item nd-item-high" data-filter="high" data-status="pending">
                <div class="nd-item-left">
                    <i class="bi bi-exclamation-triangle-fill nd-icon-high"></i>
                </div>
                <div class="nd-item-body">
                    <div class="nd-item-top">
                        <strong class="nd-item-title">Suhu Permukaan Air Tambak Tinggi</strong>
                        <span class="nd-item-time">10 menit lalu</span>
                    </div>
                    <p class="nd-item-desc">Suhu air mencapai 32,4°C. Mist Cooler telah dipicu otomatis oleh sistem inferensi fuzzy untuk mencegah stres termal pada kepiting.</p>
                    <div class="nd-item-footer">
                        <span class="nd-item-device">Unit 01 — Apartemen Kepiting Soka A1</span>
                        <button class="nd-btn-confirm" data-nd-confirm>Konfirmasi</button>
                    </div>
                </div>
            </article>

            {{-- Item: Tinggi --}}
            <article class="nd-item nd-item-high" data-filter="high" data-status="pending">
                <div class="nd-item-left">
                    <i class="bi bi-exclamation-triangle-fill nd-icon-high"></i>
                </div>
                <div class="nd-item-body">
                    <div class="nd-item-top">
                        <strong class="nd-item-title">Koneksi Perangkat Node Terputus</strong>
                        <span class="nd-item-time">3 jam lalu</span>
                    </div>
                    <p class="nd-item-desc">Perangkat kehilangan sinyal telemetri selama lebih dari 180 menit. Tegangan baterai terakhir tercatat rendah (11.2V).</p>
                    <div class="nd-item-footer">
                        <span class="nd-item-device">Unit 04 — Apartemen Box Cadangan D4</span>
                        <button class="nd-btn-confirm" data-nd-confirm>Konfirmasi</button>
                    </div>
                </div>
            </article>

            {{-- Item: Sedang —sudah selesai --}}
            <article class="nd-item nd-item-medium" data-filter="medium" data-status="done">
                <div class="nd-item-left">
                    <i class="bi bi-info-circle-fill nd-icon-medium"></i>
                </div>
                <div class="nd-item-body">
                    <div class="nd-item-top">
                        <strong class="nd-item-title">Oksigen Terlarut Mendekati Batas Kritis</strong>
                        <span class="nd-item-time">1 jam lalu</span>
                    </div>
                    <p class="nd-item-desc">Kadar DO berada pada 5,40 mg/L (cukup). Aerator Venturi dipertahankan aktif 85% duty cycle.</p>
                    <div class="nd-item-footer">
                        <span class="nd-item-device">Unit 01 — Apartemen Kepiting Soka A1</span>
                        <span class="nd-btn-done"><i class="bi bi-check-circle-fill"></i> Selesai</span>
                    </div>
                </div>
            </article>

            {{-- Item: Info —sudah selesai --}}
            <article class="nd-item nd-item-medium" data-filter="medium" data-status="done">
                <div class="nd-item-left">
                    <i class="bi bi-info-circle-fill nd-icon-medium"></i>
                </div>
                <div class="nd-item-body">
                    <div class="nd-item-top">
                        <strong class="nd-item-title">Pemberian Pakan Terjadwal Selesai</strong>
                        <span class="nd-item-time">5 jam lalu</span>
                    </div>
                    <p class="nd-item-desc">Dispenser pakan menyebarkan 250 gram pelet nutrisi kepiting soka sesuai jadwal pagi pukul 06:00 WIB.</p>
                    <div class="nd-item-footer">
                        <span class="nd-item-device">Unit 02 — Kolam Pembesaran B2</span>
                        <span class="nd-btn-done"><i class="bi bi-check-circle-fill"></i> Selesai</span>
                    </div>
                </div>
            </article>

        </div>

        {{-- Footer Drawer --}}
        <div class="nd-footer">
            <i class="bi bi-cpu nd-footer-icon"></i>
            <span>Notifikasi otomatis dihasilkan oleh rule engine AquaCrab IoT</span>
        </div>

    </aside>

</div>{{-- /.app --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ───── Sidebar Device Dropdown (existing) ─────
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

    // ───── Notification Drawer ─────
    const drawer  = document.getElementById('notifDrawer');
    const overlay = document.getElementById('notifDrawerOverlay');
    const closeBtn = document.getElementById('notifDrawerClose');
    const ndList  = document.getElementById('ndList');
    const ndSubtitle = document.getElementById('ndSubtitle');

    function openDrawer() {
        drawer.classList.add('open');
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        drawer.classList.remove('open');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    // Semua tombol lonceng di halaman (class btn-notif-bell) membuka drawer
    document.querySelectorAll('.btn-notif-bell').forEach(function(bell) {
        bell.addEventListener('click', function(e) {
            e.preventDefault();
            openDrawer();
        });
    });

    if (closeBtn)  closeBtn.addEventListener('click', closeDrawer);
    if (overlay)   overlay.addEventListener('click', closeDrawer);

    // ESC menutup drawer
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDrawer();
    });

    // ───── Tab Filter Drawer ─────
    document.querySelectorAll('.nd-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.nd-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;
            document.querySelectorAll('#ndList .nd-item').forEach(function(item) {
                item.style.display = (filter === 'all' || item.dataset.filter === filter) ? '' : 'none';
            });
        });
    });

    // ───── Konfirmasi per Item ─────
    function countPending() {
        return document.querySelectorAll('#ndList .nd-item[data-status="pending"]').length;
    }

    function updateSubtitle() {
        const n = countPending();
        if (ndSubtitle) ndSubtitle.textContent = n > 0 ? n + ' belum dikonfirmasi' : 'Semua sudah dikonfirmasi';

        // Perbarui badge lonceng di semua header
        document.querySelectorAll('.notif-badge-dot').forEach(function(badge) {
            badge.textContent = n;
            badge.style.display = n === 0 ? 'none' : '';
        });
    }

    function confirmItem(item) {
        if (!item || item.dataset.status === 'done') return;
        item.dataset.status = 'done';

        const footer = item.querySelector('.nd-item-footer');
        if (footer) {
            const btn = footer.querySelector('[data-nd-confirm]');
            if (btn) {
                btn.outerHTML = '<span class="nd-btn-done"><i class="bi bi-check-circle-fill"></i> Selesai</span>';
            }
        }

        // Tambah class visual selesai
        item.classList.add('nd-item-done');
        updateSubtitle();
    }

    // Event delegation untuk tombol Konfirmasi
    if (ndList) {
        ndList.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-nd-confirm]');
            if (btn) {
                confirmItem(btn.closest('.nd-item'));
            }
        });
    }

    // Tandai Semua Selesai
    const ndMarkAll = document.getElementById('ndMarkAll');
    if (ndMarkAll) {
        ndMarkAll.addEventListener('click', function() {
            document.querySelectorAll('#ndList .nd-item[data-status="pending"]').forEach(function(item) {
                confirmItem(item);
            });
        });
    }

    // Inisialisasi subtitle
    updateSubtitle();
});
</script>
@stack('scripts')
</body>
</html>