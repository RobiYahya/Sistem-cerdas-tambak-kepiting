@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $statusLabel = ['normal' => 'NORMAL', 'cukup' => 'CUKUP', 'waspada' => 'WASPADA', 'kritis' => 'KRITIS'];
    $keliling = 2 * M_PI * 100;
    $isi = $keliling * ($kualitas['indeks'] / 100);
@endphp

@section('content')

{{-- =========================================================================
     1. HEADER UTAMA DASHBOARD
     ========================================================================= --}}
<header class="page-head">
    @if ($selectedDevice)
        <div class="wadah-detail-breadcrumb">
            <a href="{{ route('dashboard') }}" class="wdb-link">Perangkat Saya</a>
            <i class="bi bi-chevron-right wdb-sep"></i>
            <span class="wdb-device">{{ Str::limit($selectedDevice['nama'], 26) }}</span>
            <i class="bi bi-chevron-right wdb-sep"></i>
            <span class="wdb-current">Dashboard Wadah</span>
        </div>
        <div class="page-head-bar wadah-head-bar">
            <div class="header-wadah-title-wrap">
                <h1 class="wadah-title-detail">
                    Dashboard Wadah &mdash; {{ $selectedDevice['nama'] }}
                    <span class="badge-role-pill">{{ ($userRole ?? session('user_role', 'teknisi')) === 'teknisi' ? 'TEKNISI' : 'NELAYAN' }}</span>
                    <span class="combined-telemetry-pill">
                        <span class="ctp-item"><i class="bi bi-wifi"></i> {{ $selectedDevice['rssi'] ?? '-68 dBm' }}</span>
                        <span class="ctp-sep">&bull;</span>
                        <span class="ctp-item"><i class="bi bi-battery-charging"></i> {{ $selectedDevice['baterai'] ?? '94%' }}</span>
                    </span>
                </h1>
                <p class="wadah-subtitle-detail">Pemantauan telemetri real-time 6 parameter dan status kesehatan kepiting pada {{ $selectedDevice['nama'] }}.</p>
            </div>
            <div class="wadah-head-actions">
                <button type="button" class="btn-notif-bell" title="Notifikasi Wadah (2 Baru)">
                    <i class="bi bi-bell"></i>
                    <span class="notif-badge-dot">2</span>
                </button>
                <div class="header-device-selector-pill">
                    <i class="bi bi-stack hdsp-icon"></i>
                    <span class="hdsp-name">{{ $selectedDevice['nama'] }}</span>
                    <span class="hdsp-badge">BAIK</span>
                    <i class="bi bi-chevron-down hdsp-chevron"></i>
                    <select class="hdsp-select" onchange="window.location.href='{{ route('dashboard') }}?device=' + this.value" title="Ganti Wadah">
                        @foreach ($devices as $dev)
                            <option value="{{ $dev['id'] }}" {{ $dev['id'] === $selectedDevice['id'] ? 'selected' : '' }}>
                                {{ $dev['nama'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @else
        <div class="wadah-breadcrumb">Perangkat Saya</div>
        <div class="page-head-bar wadah-head-bar">
            <div>
                <h1 class="wadah-main-title">
                    @if (($userRole ?? session('user_role', 'teknisi')) === 'teknisi')
                        Manajemen Perangkat Wadah
                        <span class="badge-role-pill">TEKNISI</span>
                    @else
                        Perangkat Saya
                        <span class="badge-role-pill">NELAYAN</span>
                    @endif
                </h1>
                <p>
                    @if (($userRole ?? session('user_role', 'teknisi')) === 'teknisi')
                        Daftar seluruh unit jerigen dan wadah budidaya tambak. Dukungan multi-wadah dan pendaftaran node baru.
                    @else
                        Daftar wadah jerigen 20 liter dan apartemen box kepiting binaan Anda. Pilih wadah untuk mulai memantau.
                    @endif
                </p>
            </div>
            <div class="wadah-head-actions">
                <button type="button" class="btn-notif-bell" title="Notifikasi Alert (2 Unread)">
                    <i class="bi bi-bell"></i>
                    <span class="notif-badge-dot">2</span>
                </button>
                <span class="pill-counter-box">
                    {{ $totalDevices }} Wadah Terdaftar
                </span>
            </div>
        </div>
    @endif
</header>


{{-- =========================================================================
     2. JIKA SEDANG MEMBUKA DETAIL MONITORING SATU PERANGKAT (?device=...)
     ========================================================================= --}}
@if ($selectedDevice)
    {{-- A. Sub-Header Kartu Wadah --}}
    <section class="device-subcard-header">
        <div class="dsh-left">
            <a href="{{ route('dashboard') }}" class="btn-circle-back" title="Kembali ke Daftar Perangkat">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="dsh-title-group">
                <div class="dsh-top-meta">
                    <span class="wadah-id-chip">{{ $selectedDevice['id'] }}</span>
                    <span class="dsh-type-text">&bull; {{ $selectedDevice['tipe'] ?? 'Apartemen Box Vertikal' }}</span>
                </div>
                <h2 class="dsh-main-title">Dashboard {{ $selectedDevice['nama'] }}</h2>
                <div class="dsh-meta-row">
                    <span class="dsh-meta-item"><i class="bi bi-geo-alt"></i> {{ $selectedDevice['lokasi'] }}</span>
                    <span class="dsh-meta-sep">&bull;</span>
                    <span class="dsh-meta-item">Petambak: <strong>{{ $selectedDevice['petambak'] ?? 'Pak Robi (Petambak Binaan)' }}</strong></span>
                    <span class="dsh-meta-sep">&bull;</span>
                    <span class="dsh-meta-item">Populasi: <strong>{{ $selectedDevice['populasi'] ?? '850 ekor' }}</strong></span>
                </div>
            </div>
        </div>

        <div class="dsh-right">
            <div class="dsh-tabs">
                <button type="button" class="dsh-tab active">Telemetri Real-Time</button>
                <button type="button" class="dsh-tab btn-trigger-info-node"
                        data-id="{{ $selectedDevice['id'] }}"
                        data-nama="{{ $selectedDevice['nama'] }}"
                        data-lokasi="{{ $selectedDevice['lokasi'] }}"
                        data-token="{{ $selectedDevice['token'] }}"
                        data-status="{{ $selectedDevice['status'] }}"
                        data-last="{{ $selectedDevice['last_seen'] }}">
                    Informasi Perangkat
                </button>
            </div>
            <span class="wadah-status-pill online"><i class="dot"></i> ONLINE</span>
        </div>
    </section>

    {{-- B. Banner Peringatan Kritis Suhu --}}
    <section class="alert-banner-amber">
        <div class="aba-left">
            <div class="aba-icon"><i class="bi bi-shield-exclamation"></i></div>
            <div class="aba-content">
                <strong>Peringatan Suhu Permukaan {{ $selectedDevice['nama'] }}: 32,4°C</strong>
                <p>Suhu melewati ambang batas optimal (31,0°C). Mist Cooler dipicu otomatis oleh sistem inferensi fuzzy.</p>
            </div>
        </div>
        <button type="button" class="aba-btn">Lihat Peringatan</button>
    </section>

    {{-- C. Status Kualitas Air (Green Hero Card Sesuai Mockup) --}}
    <section class="card-status-forest">
        <div class="csf-head">
            <div>
                <h2>Status Kualitas Air</h2>
                <p>Indeks kualitas air berdasarkan data terbaru.</p>
            </div>
            <span class="csf-badge-white"><i class="dot"></i> BAIK</span>
        </div>

        <div class="csf-center">
            <div class="csf-left-val">
                <span class="csf-val-lbl">Nilai indeks</span>
                <strong class="csf-val-big">{{ number_format($kualitas['indeks'], 1, ',', '.') }}</strong>
            </div>

            <div class="csf-gauge">
                <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="60" cy="60" r="48" fill="none"
                        stroke="rgba(255,255,255,0.18)" stroke-width="12"/>
                    <circle cx="60" cy="60" r="48" fill="none"
                        stroke="#ccf832" stroke-width="12" stroke-linecap="round"
                        stroke-dasharray="{{ round(2 * 3.14159 * 48 * $kualitas['indeks'] / 100, 2) }} {{ round(2 * 3.14159 * 48, 2) }}"
                        transform="rotate(-90 60 60)"/>
                </svg>
                <div class="csf-gauge-text">
                    <strong>{{ number_format($kualitas['indeks'], 1, ',', '.') }}</strong>
                    <span>Indeks Kualitas</span>
                </div>
            </div>
        </div>

        <div class="csf-subcards">
            @foreach ($kualitas['sub'] as $sub)
                <div class="csf-subcard">
                    <span class="csfs-lbl">{{ $sub['label'] }}</span>
                    <strong class="csfs-val">{{ $sub['nilai'] }}%</strong>
                </div>
            @endforeach
        </div>
    </section>

    {{-- D. Enam Parameter Kualitas Air Wadah --}}
    <section class="card-six-parameters">
        <div class="csp-head">
            <div>
                <h3>Enam Parameter Kualitas Air Wadah</h3>
                <p>Nilai masukan dan hasil fuzzifikasi untuk probe sensor pada {{ $selectedDevice['nama'] }}.</p>
            </div>
            <a href="{{ route('inferensi-fuzzy') }}" class="csp-link-fuzzy">
                <span>Telusuri Fuzzifikasi</span>
                <i class="bi bi-arrow-up-right"></i>
            </a>
        </div>

        <div class="csp-grid">
            @foreach ($sixParameters as $param)
                <div class="csp-item-card">
                    <span class="csp-param-name">{{ $param['label'] }}</span>
                    <div class="csp-param-val">
                        <strong>{{ $param['nilai'] }}</strong>
                        @if ($param['satuan'])
                            <small>{{ $param['satuan'] }}</small>
                        @endif
                    </div>
                    <span class="csp-param-fuzzy">{{ $param['fuzzy'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- E. Split Grid: Grafik Tren Telemetri (Kiri) & Status Aktuator (Kanan) --}}
    <div class="dashboard-split-row">
        {{-- Kolom Kiri: Grafik Tren Telemetri Kualitas Air --}}
        <section class="card-chart-telemetri">
            <div class="cct-head">
                <div>
                    <h3>Grafik Tren Telemetri Kualitas Air</h3>
                    <p>Fluktuasi 24 jam terakhir pada {{ $selectedDevice['nama'] }}</p>
                </div>
                <div class="cct-filter-tabs" id="chartFilterTabs">
                    <button type="button" class="cct-filter-btn active" data-filter="all">Semua</button>
                    <button type="button" class="cct-filter-btn" data-filter="ph">pH</button>
                    <button type="button" class="cct-filter-btn" data-filter="do">DO</button>
                    <button type="button" class="cct-filter-btn" data-filter="salinitas">Salinitas</button>
                    <button type="button" class="cct-filter-btn" data-filter="suhu">Suhu</button>
                </div>
            </div>

            <div class="cct-canvas-wrap">
                <canvas id="chartTrenTelemetri" height="260"></canvas>
            </div>

            <div class="cct-legend-row">
                <div class="cct-legend-item text-green">
                    <span class="cct-legend-line" style="background: #10b981;"></span>
                    <span>pH (8,48)</span>
                </div>
                <div class="cct-legend-item text-cyan">
                    <span class="cct-legend-line" style="background: #0284c7;"></span>
                    <span>DO (5,40 mg/L)</span>
                </div>
                <div class="cct-legend-item text-purple">
                    <span class="cct-legend-line" style="background: #8b5cf6;"></span>
                    <span>Salinitas (26,4 ppt)</span>
                </div>
                <div class="cct-legend-item text-orange">
                    <span class="cct-legend-line" style="background: #f97316;"></span>
                    <span>Suhu (32,4 &deg;C)</span>
                </div>
            </div>
        </section>

        {{-- Kolom Kanan: Status Aktuator Wadah --}}
        <section class="card-aktuator-wadah">
            <div class="caw-head">
                <div>
                    <h3>Status Aktuator Wadah</h3>
                    <p>Kendali perangkat terpasang pada {{ $selectedDevice['id'] }}</p>
                </div>
                <span class="caw-icon-bolt"><i class="bi bi-lightning-charge-fill"></i></span>
            </div>

            <div class="caw-list">
                @foreach ($aktuators as $akt)
                    <div class="caw-item">
                        <div class="cawi-info">
                            <strong>{{ $akt['nama'] }}</strong>
                            <span>{{ $akt['sub'] }}</span>
                        </div>
                        <span class="badge-akt-{{ strtolower($akt['state']) }}">{{ $akt['state'] }}</span>
                    </div>
                @endforeach
            </div>

            @if (($userRole ?? session('user_role', 'teknisi')) === 'teknisi')
                <a href="{{ route('kontrol-aktuator') }}" class="btn-open-panel-aktuator">
                    <span>Buka Panel Kontrol 4 Aktuator & Otomasi</span>
                </a>
            @endif
        </section>
    </div>


{{-- =========================================================================
     3. TAMPILAN UTAMA: MANAJEMEN MULTI-DEVICE (KONDISI A, B, & C)
     ========================================================================= --}}
@else

    {{-- KONDISI A: BELUM ADA PERANGKAT (0 PERANGKAT) --}}
    @if ($totalDevices === 0)
        <section class="card-empty-device">
            @include('partials.crab-onboarding')
            <h3>Belum ada perangkat tambak kepiting</h3>
            <p>Tambahkan perangkat untuk memantau enam parameter air: pH, oksigen terlarut, suhu air, level air, salinitas, dan kekeruhan.</p>
            <button type="button" class="btn-primary-lime btn-trigger-tambah">
                <i class="bi bi-plus-lg"></i>
                <span>Tambahkan perangkat pertama</span>
            </button>
        </section>

        <section class="three-steps-section">
            <h3>Tiga langkah untuk mulai</h3>
            <div class="three-steps-grid">
                <div class="step-card">
                    <div class="step-card-head">
                        <span class="step-icon"><i class="bi bi-plug"></i></span>
                        <h4>Pasang sensor air tambak</h4>
                    </div>
                    <p>Pasang sensor pada air tambak kepiting, lalu periksa catu daya perangkat.</p>
                </div>

                <div class="step-card">
                    <div class="step-card-head">
                        <span class="step-icon"><i class="bi bi-wifi"></i></span>
                        <h4>Hubungkan ke jaringan</h4>
                    </div>
                    <p>Siapkan nama jaringan dan kata sandi Wi-Fi untuk perangkat tambak kepiting.</p>
                </div>

                <div class="step-card">
                    <div class="step-card-head">
                        <span class="step-icon"><i class="bi bi-activity"></i></span>
                        <h4>Pantau enam parameter air</h4>
                    </div>
                    <p>Lihat pH, oksigen terlarut, suhu air, level air, salinitas, dan kekeruhan di dashboard.</p>
                </div>
            </div>

            <p class="empty-footer-note">
                Indeks kualitas air, tren parameter, dan notifikasi akan tersedia setelah perangkat tambak terhubung.
            </p>
        </section>

    {{-- KONDISI B & C: SUDAH MEMILIKI PERANGKAT (1 - 5 ATAU 10 - 20+ WADAH) --}}
    @else
        {{-- Banner Wadah Budidaya (RBAC: Nelayan vs Teknisi) --}}
        <section class="wadah-hero-card">
            <div class="whc-content">
                <h3>
                    @if (($userRole ?? session('user_role', 'teknisi')) === 'teknisi')
                        Manajemen Perangkat Wadah Tambak
                        <span class="badge-role-lime">TEKNISI</span>
                    @else
                        Perangkat Saya (Wadah Budidaya)
                        <span class="badge-role-lime">NELAYAN</span>
                    @endif
                </h3>
                <p>
                    @if (($userRole ?? session('user_role', 'teknisi')) === 'teknisi')
                        Pantau status koneksi telemetri dan konfigurasi seluruh unit node budidaya kepiting soka.
                    @else
                        Pilih wadah jerigen 20 liter atau modul apartemen box untuk membuka ruang kerja pemantauan.
                    @endif
                </p>
            </div>
            @if (($userRole ?? session('user_role', 'teknisi')) === 'teknisi')
                <button type="button" class="btn-add-device-hero btn-trigger-tambah" title="Daftarkan Node Perangkat Baru">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Perangkat Baru</span>
                </button>
            @endif
        </section>

        {{-- 4 Metric Summary Cards --}}
        <section class="wadah-stats-grid">
            <div class="wstat-card">
                <span class="wstat-label">Total Wadah Budidaya</span>
                <div class="wstat-value-row">
                    <strong class="wstat-num">{{ $totalDevices }}</strong>
                    <span class="wstat-unit">Unit</span>
                </div>
                <span class="wstat-sub">Node telemetri terpasang</span>
            </div>

            <div class="wstat-card">
                <span class="wstat-label">Kondisi Air Optimal</span>
                <div class="wstat-value-row">
                    <strong class="wstat-num text-green">{{ $optimalCount }}</strong>
                    <span class="wstat-unit">Wadah</span>
                </div>
                <span class="wstat-sub text-green">Skor kualitas &ge; 70 (Aman)</span>
            </div>

            <div class="wstat-card">
                <span class="wstat-label">Perlu Perhatian</span>
                <div class="wstat-value-row">
                    <strong class="wstat-num {{ $warningCount > 0 ? 'text-amber' : '' }}">{{ $warningCount }}</strong>
                    <span class="wstat-unit">Wadah</span>
                </div>
                <span class="wstat-sub {{ $warningCount > 0 ? 'text-amber' : '' }}">
                    {{ $warningCount > 0 ? 'Butuh inspeksi sirkulasi/suhu' : 'Semua parameter normal' }}
                </span>
            </div>

            <div class="wstat-card">
                <span class="wstat-label">Rata-Rata Kualitas Klaster</span>
                <div class="wstat-value-row">
                    <strong class="wstat-num">{{ $avgQuality }}</strong>
                    <span class="wstat-unit">/ 100</span>
                </div>
                <span class="wstat-sub">Indeks gabungan klaster</span>
            </div>
        </section>

        {{-- Toolbar Pencarian Real-Time & Counter --}}
        <div class="wadah-toolbar">
            <div class="wadah-search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="deviceSearchInput" class="wadah-search-input" placeholder="Cari ID, nama wadah, atau lokasi...">
            </div>
            <div class="wadah-count-info">
                Menampilkan <span id="displayedCount">{{ $totalDevices }}</span> dari {{ $totalDevices }} unit wadah
            </div>
        </div>

        {{-- Grid Kartu Wadah (3 Kolom Desktop Sesuai Desain Mockup) --}}
        <div class="wadah-cards-grid" id="devicesGrid">
            @foreach ($devices as $index => $dev)
                @php
                    $indeksFloat = (float) str_replace(',', '.', $dev['indeks'] ?? 0);
                    $indeksFmt = number_format($indeksFloat, 1, ',', '.');
                    $kualitasUpper = strtoupper($dev['kualitas'] ?? 'BAIK');
                    $badgeColorClass = match($kualitasUpper) {
                        'SANGAT BAIK', 'OPTIMAL' => 'status-pill-green',
                        'BAIK'                   => 'status-pill-green',
                        'CUKUP', 'NORMAL'        => 'status-pill-cyan',
                        'WASPADA'                => 'status-pill-amber',
                        'KRITIS'                 => 'status-pill-red',
                        default                  => 'status-pill-green',
                    };
                @endphp
                <article class="wadah-card device-list-item"
                         data-name="{{ strtolower($dev['nama']) }}"
                         data-id="{{ strtolower($dev['id']) }}"
                         data-status="{{ $dev['status'] }}"
                         data-index="{{ $index }}">
                    
                    {{-- Baris Atas: Chip ID & Status Online/Offline --}}
                    <div class="wadah-card-top">
                        <span class="wadah-id-chip">{{ $dev['id'] }}</span>
                        <span class="wadah-status-pill {{ $dev['status'] === 'online' ? 'online' : 'offline' }}">
                            <i class="dot"></i> {{ strtoupper($dev['status']) }}
                        </span>
                    </div>

                    {{-- Judul Wadah & Lokasi --}}
                    <h3 class="wadah-card-title">{{ $dev['nama'] }}</h3>
                    <div class="wadah-card-loc">
                        <i class="bi bi-geo-alt"></i>
                        <span>{{ $dev['lokasi'] }}</span>
                    </div>

                    {{-- Kotak Nilai Kualitas Air --}}
                    <div class="wadah-quality-box">
                        <div class="wq-left">
                            <span class="wq-label">Status Kualitas Air</span>
                            <strong class="wq-score">{{ $indeksFmt }}</strong>
                        </div>
                        <div class="wq-right">
                            <span class="wq-status-badge {{ $badgeColorClass }}">
                                {{ $kualitasUpper }}
                            </span>
                        </div>
                    </div>

                    {{-- Baris Info Pembaruan Terakhir (Tanpa Petambak Binaan & Tanpa Populasi Kepiting) --}}
                    <div class="wadah-info-table">
                        <div class="wit-row">
                            <span class="wit-label">Pembaruan Terakhir:</span>
                            <code class="wit-mono">{{ $dev['last_seen'] }}</code>
                        </div>
                    </div>

                    {{-- Footer Aksi: Info Node & Pantau Wadah Ini --}}
                    <div class="wadah-card-footer">
                        <button type="button" class="btn-info-node btn-trigger-info-node"
                                data-id="{{ $dev['id'] }}"
                                data-nama="{{ $dev['nama'] }}"
                                data-lokasi="{{ $dev['lokasi'] }}"
                                data-token="{{ $dev['token'] }}"
                                data-status="{{ $dev['status'] }}"
                                data-last="{{ $dev['last_seen'] }}"
                                title="Informasi & Kredensial Node">
                            <i class="bi bi-info-circle"></i>
                            <span>Info Node</span>
                        </button>

                        <a href="{{ route('dashboard', ['device' => $dev['id']]) }}" class="btn-pantau-wadah" title="Buka Monitoring Wadah Ini">
                            <span>Pantau Wadah Ini</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pesan Jika Pencarian Tidak Ditemukan --}}
        <div id="noDevicesFound" style="display: none; background: #ffffff; border: 1px dashed var(--line); border-radius: 18px; padding: 48px 24px; text-align: center; margin-top: 14px;">
            <i class="bi bi-search" style="font-size: 32px; color: #94a3b8; display: block; margin-bottom: 12px;"></i>
            <h4 style="font-size: 16px; font-weight: 700; color: var(--ink); margin-bottom: 6px;">Wadah tidak ditemukan</h4>
            <p style="font-size: 13.5px; color: var(--muted);">Tidak ada wadah budidaya yang cocok dengan kata kunci pencarian.</p>
        </div>

        {{-- Paginasi untuk Skalabilitas Banyak Perangkat (10 - 20+) --}}
        @if ($totalDevices > 8)
            <div class="table-foot" style="margin-top: 22px;">
                <span class="foot-info" id="deviceFootInfo">
                    Menampilkan 1–8 dari {{ $totalDevices }} perangkat
                </span>

                <div class="riwayat-pager" id="devicePagination">
                    <button class="pg-btn" id="devPgPrev" aria-label="Sebelumnya"><i class="bi bi-chevron-left"></i></button>
                    @for ($p = 0; $p < ceil($totalDevices / 8); $p++)
                        <button class="pg-btn {{ $p === 0 ? 'active' : '' }}" data-page="{{ $p }}">{{ $p + 1 }}</button>
                    @endfor
                    <button class="pg-btn" id="devPgNext" aria-label="Berikutnya"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        @endif

    @endif

@endif

{{-- =========================================================================
     4. MODAL-MODAL INTERAKTIF (TAMBAH, EDIT, HAPUS PERANGKAT)
     ========================================================================= --}}
@include('partials.modal-tambah-perangkat')
@include('partials.modal-edit-perangkat')
@include('partials.modal-hapus-perangkat')
@include('partials.modal-info-node')

@endsection

@push('scripts')
{{-- Script Grafik jika sedang membuka detail --}}
@if ($selectedDevice)
<script>
    window.TELEMETRI_TREND = @json($telemetriTrend);
</script>
<script src="{{ asset('js/dashboard.js') }}?v={{ @filemtime(public_path('js/dashboard.js')) ?: time() }}"></script>
@endif

{{-- Script Manajemen Multi-Device (Search, Filter, Paginasi, Modal Edit & Hapus) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Trigger Buka Modal Tambah Perangkat
    const modalTambah = document.getElementById('modalTambahPerangkat');
    document.querySelectorAll('.btn-trigger-tambah').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (modalTambah) {
                modalTambah.classList.add('ka-modal-open');
                modalTambah.setAttribute('aria-hidden', 'false');
            }
        });
    });

    const btnTutupTambah = document.getElementById('btnTutupModal');
    if (btnTutupTambah && modalTambah) {
        btnTutupTambah.addEventListener('click', function () {
            modalTambah.classList.remove('ka-modal-open');
            modalTambah.setAttribute('aria-hidden', 'true');
        });
    }

    // 2. Modal Edit Perangkat
    const modalEdit = document.getElementById('modalEditPerangkat');
    const formEdit = document.getElementById('formEditPerangkat');
    const inputEditNama = document.getElementById('editNamaPerangkat');
    const inputEditLokasi = document.getElementById('editLokasiPerangkat');
    const btnBatalEdit = document.getElementById('btnBatalEdit');

    document.querySelectorAll('.btn-edit-device').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const lokasi = this.getAttribute('data-lokasi');

            if (formEdit) formEdit.action = '/perangkat/update/' + encodeURIComponent(id);
            if (inputEditNama) inputEditNama.value = nama;
            if (inputEditLokasi) inputEditLokasi.value = lokasi;

            if (modalEdit) {
                modalEdit.classList.add('ka-modal-open');
                modalEdit.setAttribute('aria-hidden', 'false');
            }
        });
    });

    if (btnBatalEdit && modalEdit) {
        btnBatalEdit.addEventListener('click', function () {
            modalEdit.classList.remove('ka-modal-open');
            modalEdit.setAttribute('aria-hidden', 'true');
        });
    }

    // 3. Modal Hapus Perangkat
    const modalHapus = document.getElementById('modalHapusPerangkat');
    const formHapus = document.getElementById('formHapusPerangkat');
    const labelHapusNama = document.getElementById('hapusNamaPerangkat');
    const btnBatalHapus = document.getElementById('btnBatalHapus');

    document.querySelectorAll('.btn-delete-device').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');

            if (formHapus) formHapus.action = '/perangkat/delete/' + encodeURIComponent(id);
            if (labelHapusNama) labelHapusNama.textContent = nama + ' (' + id + ')';

            if (modalHapus) {
                modalHapus.classList.add('ka-modal-open');
                modalHapus.setAttribute('aria-hidden', 'false');
            }
        });
    });

    if (btnBatalHapus && modalHapus) {
        btnBatalHapus.addEventListener('click', function () {
            modalHapus.classList.remove('ka-modal-open');
            modalHapus.setAttribute('aria-hidden', 'true');
        });
    }

    // Modal Info Node Telemetri
    const modalInfoNode = document.getElementById('modalInfoNode');
    const infoNodeTitle = document.getElementById('infoNodeTitle');
    const infoNodeId = document.getElementById('infoNodeId');
    const infoNodeLokasi = document.getElementById('infoNodeLokasi');
    const infoNodeStatus = document.getElementById('infoNodeStatus');
    const infoNodeToken = document.getElementById('infoNodeToken');
    const btnCopyInfoToken = document.getElementById('btnCopyInfoToken');
    const btnInfoEdit = document.getElementById('btnInfoEdit');
    const btnInfoDelete = document.getElementById('btnInfoDelete');
    const btnTutupInfoNode = document.getElementById('btnTutupInfoNode');

    document.querySelectorAll('.btn-trigger-info-node').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id') || '-';
            const nama = this.getAttribute('data-nama') || '-';
            const lokasi = this.getAttribute('data-lokasi') || '-';
            const token = this.getAttribute('data-token') || '-';
            const status = this.getAttribute('data-status') || 'online';

            if (infoNodeTitle) infoNodeTitle.textContent = nama;
            if (infoNodeId) infoNodeId.textContent = id;
            if (infoNodeLokasi) infoNodeLokasi.textContent = lokasi;
            if (infoNodeStatus) {
                const isOnline = status.toLowerCase() === 'online';
                infoNodeStatus.textContent = isOnline ? '🟢 ONLINE' : '⚪ OFFLINE';
                infoNodeStatus.style.color = isOnline ? '#16a34a' : '#64748b';
            }
            if (infoNodeToken) infoNodeToken.textContent = token;
            if (btnCopyInfoToken) btnCopyInfoToken.setAttribute('data-token', token);

            if (btnInfoEdit) {
                btnInfoEdit.setAttribute('data-id', id);
                btnInfoEdit.setAttribute('data-nama', nama);
                btnInfoEdit.setAttribute('data-lokasi', lokasi);
            }
            if (btnInfoDelete) {
                btnInfoDelete.setAttribute('data-id', id);
                btnInfoDelete.setAttribute('data-nama', nama);
            }

            if (modalInfoNode) {
                modalInfoNode.classList.add('ka-modal-open');
                modalInfoNode.setAttribute('aria-hidden', 'false');
            }
        });
    });

    if (btnTutupInfoNode && modalInfoNode) {
        btnTutupInfoNode.addEventListener('click', function () {
            modalInfoNode.classList.remove('ka-modal-open');
            modalInfoNode.setAttribute('aria-hidden', 'true');
        });
    }

    if (btnInfoEdit && modalInfoNode) {
        btnInfoEdit.addEventListener('click', function () {
            modalInfoNode.classList.remove('ka-modal-open');
            modalInfoNode.setAttribute('aria-hidden', 'true');
        });
    }

    if (btnInfoDelete && modalInfoNode) {
        btnInfoDelete.addEventListener('click', function () {
            modalInfoNode.classList.remove('ka-modal-open');
            modalInfoNode.setAttribute('aria-hidden', 'true');
        });
    }

    // Tutup modal jika klik backdrop
    [modalTambah, modalEdit, modalHapus, modalInfoNode].forEach(function (m) {
        if (!m) return;
        m.addEventListener('click', function (e) {
            if (e.target === m) {
                m.classList.remove('ka-modal-open');
                m.setAttribute('aria-hidden', 'true');
            }
        });
    });

    // 4. Pencarian & Filter Real-Time Perangkat
    const searchInput   = document.getElementById('deviceSearchInput');
    const filterBtns    = document.querySelectorAll('.filter-chip-btn');
    // Gunakan .device-list-item untuk horizontal list layout
    const cards         = document.querySelectorAll('.device-list-item');
    const noFound       = document.getElementById('noDevicesFound');

    // Paginasi: 8 item per halaman
    const PAGE_SIZE      = 8;
    let currentFilter    = 'all';
    let searchQuery      = '';
    let currentPage      = 0;

    // Ambil elemen paginasi
    const pgPrev         = document.getElementById('devPgPrev');
    const pgNext         = document.getElementById('devPgNext');
    const pgBtns         = document.querySelectorAll('#devicePagination [data-page]');
    const footInfo       = document.getElementById('deviceFootInfo');

    function applyFilterAndSearch() {
        // Kumpulkan item yang cocok filter + search
        let matched = [];
        cards.forEach(function (card) {
            const name   = card.getAttribute('data-name') || '';
            const id     = card.getAttribute('data-id') || '';
            const status = card.getAttribute('data-status') || '';

            const matchesSearch = !searchQuery || name.includes(searchQuery) || id.includes(searchQuery);
            const matchesFilter = (currentFilter === 'all') || (status === currentFilter);

            if (matchesSearch && matchesFilter) {
                matched.push(card);
                card.style.display = 'none'; // akan diatur oleh paginasi
            } else {
                card.style.display = 'none';
            }
        });

        // Reset halaman ke 0 setiap kali filter/search berubah
        currentPage = 0;
        renderPage(matched);

        if (noFound) {
            noFound.style.display = (matched.length === 0 && cards.length > 0) ? 'block' : 'none';
        }
    }

    function renderPage(matched) {
        // Sembunyikan semua dulu
        cards.forEach(c => c.style.display = 'none');

        if (!matched || matched.length === 0) return;

        const start = currentPage * PAGE_SIZE;
        const end   = Math.min(start + PAGE_SIZE, matched.length);

        for (let i = start; i < end; i++) {
            matched[i].style.display = '';
        }

        // Update info teks
        if (footInfo) {
            footInfo.textContent = 'Menampilkan ' + (start + 1) + '–' + end + ' dari ' + matched.length + ' perangkat';
        }
        const displayedCount = document.getElementById('displayedCount');
        if (displayedCount) {
            displayedCount.textContent = matched.length;
        }

        // Aktifkan tombol paginasi yang sesuai
        pgBtns.forEach(function (btn) {
            const pg = parseInt(btn.getAttribute('data-page'), 10);
            btn.classList.toggle('active', pg === currentPage);
        });
        if (pgPrev) pgPrev.disabled = (currentPage === 0);
        if (pgNext) pgNext.disabled = (end >= matched.length);
    }

    // Jalankan filter + paginasi pertama kali (tampilkan semua, halaman 0)
    let matchedCache = [];
    function refresh() {
        matchedCache = [];
        cards.forEach(function (card) {
            const name   = card.getAttribute('data-name') || '';
            const id     = card.getAttribute('data-id') || '';
            const status = card.getAttribute('data-status') || '';
            const matchesSearch = !searchQuery || name.includes(searchQuery) || id.includes(searchQuery);
            const matchesFilter = (currentFilter === 'all') || (status === currentFilter);
            if (matchesSearch && matchesFilter) matchedCache.push(card);
        });
        currentPage = 0;
        renderPage(matchedCache);
        if (noFound) {
            noFound.style.display = (matchedCache.length === 0 && cards.length > 0) ? 'block' : 'none';
        }
    }
    refresh();

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            searchQuery = this.value.trim().toLowerCase();
            refresh();
        });
    }

    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter') || 'all';
            refresh();
        });
    });

    // Paginasi tombol nomor halaman
    pgBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentPage = parseInt(this.getAttribute('data-page'), 10);
            renderPage(matchedCache);
        });
    });
    if (pgPrev) {
        pgPrev.addEventListener('click', function () {
            if (currentPage > 0) { currentPage--; renderPage(matchedCache); }
        });
    }
    if (pgNext) {
        pgNext.addEventListener('click', function () {
            const totalPages = Math.ceil(matchedCache.length / PAGE_SIZE);
            if (currentPage < totalPages - 1) { currentPage++; renderPage(matchedCache); }
        });
    }

    // 5. Salin API Key ke Clipboard
    document.querySelectorAll('.btn-copy-token').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const token = this.getAttribute('data-token');
            const icon  = this.querySelector('i');
            const text  = this.querySelector('.copy-text');

            function showCopied() {
                if (icon) icon.className = 'bi bi-check2';
                if (text) text.textContent = 'Tersalin!';
                btn.classList.add('copied');
                setTimeout(function () {
                    if (icon) icon.className = 'bi bi-copy';
                    if (text) text.textContent = 'Salin';
                    btn.classList.remove('copied');
                }, 2000);
            }

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(token).then(showCopied).catch(function () {
                    fallbackCopy(token);
                });
            } else {
                fallbackCopy(token);
            }

            function fallbackCopy(val) {
                const el = document.createElement('textarea');
                el.value = val;
                el.style.position = 'fixed';
                el.style.opacity = '0';
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
                showCopied();
            }
        });
    });
});
</script>
@endpush
