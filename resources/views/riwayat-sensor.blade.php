@extends('layouts.app')

@section('title', 'Riwayat dan Laporan')

@section('content')

{{-- =========================================================================
     1. HEADER UTAMA — KONSISTEN DENGAN DASHBOARD & MOCKUP
     ========================================================================= --}}
<header class="page-head">
    <div class="wadah-detail-breadcrumb">
        <a href="{{ route('dashboard') }}" class="wdb-link">Perangkat Saya</a>
        <i class="bi bi-chevron-right wdb-sep"></i>
        <span class="wdb-device">{{ Str::limit($selectedDevice['nama'] ?? 'Unit 01', 28) }}</span>
        <i class="bi bi-chevron-right wdb-sep"></i>
        <span class="wdb-current">Riwayat dan Laporan</span>
    </div>

    <div class="page-head-bar wadah-head-bar">
        <div class="header-wadah-title-wrap">
            <h1 class="wadah-title-detail">
                Riwayat dan Laporan &mdash; {{ $selectedDevice['nama'] ?? 'Unit 01 — Apartemen Kepiting Soka A1' }}
                <span class="badge-role-pill">{{ ($userRole ?? session('user_role', 'nelayan')) === 'teknisi' ? 'TEKNISI' : 'NELAYAN' }}</span>
                <span class="combined-telemetry-pill">
                    <span class="ctp-item"><i class="bi bi-wifi"></i> {{ $selectedDevice['rssi'] ?? '-68 dBm' }}</span>
                    <span class="ctp-sep">&bull;</span>
                    <span class="ctp-item"><i class="bi bi-battery-charging"></i> {{ $selectedDevice['baterai'] ?? '94%' }}</span>
                </span>
            </h1>
            <p class="wadah-subtitle-detail">Log data telemetri historis {{ $selectedDevice['nama'] ?? 'Unit 01' }} dengan filter rentang waktu, ekspor CSV, dan unduh laporan berkala.</p>
        </div>

        <div class="wadah-head-actions">
            {{-- Tombol Lonceng (Membuka Notification Drawer) --}}
            <a href="#" class="btn-notif-bell" title="Pusat Notifikasi (2 Belum Dikonfirmasi)">
                <i class="bi bi-bell"></i>
                <span class="notif-badge-dot">2</span>
            </a>

            {{-- Pemilih Wadah (Device Selector Pill) --}}
            <div class="header-device-selector-pill">
                <i class="bi bi-stack hdsp-icon"></i>
                <span class="hdsp-name">{{ Str::limit($selectedDevice['nama'] ?? 'Unit 01', 22) }}</span>
                <span class="hdsp-badge">{{ $selectedDevice['kualitas'] ?? 'BAIK' }}</span>
                <i class="bi bi-chevron-down hdsp-chevron"></i>
                <select class="hdsp-select" onchange="window.location.href='{{ route('riwayat-sensor') }}?device=' + this.value" title="Ganti Wadah">
                    @foreach ($devices as $dev)
                        <option value="{{ $dev['id'] }}" {{ $dev['id'] === ($selectedDevice['id'] ?? '') ? 'selected' : '' }}>
                            {{ $dev['nama'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</header>

{{-- =========================================================================
     2. TOOLBAR / SUBCARD HEADER (Rentang Waktu & Aksi Ekspor / PDF)
     ========================================================================= --}}
<section class="riwayat-subcard-header">
    <div class="rsh-left">
        <h2 class="rsh-title">Riwayat dan Laporan Kualitas Air</h2>
        <p class="rsh-device">Wadah Terpilih: <strong>{{ $selectedDevice['nama'] }}</strong> <code>({{ $selectedDevice['id'] }})</code></p>
    </div>

    <div class="rsh-actions">
        {{-- Filter Rentang Waktu (1 Jam, 24 Jam, 7 Hari, 30 Hari) --}}
        <div class="rsh-range-pills" id="timeRangeFilter">
            <button type="button" class="rsh-pill" data-range="1h">1 Jam</button>
            <button type="button" class="rsh-pill active" data-range="24h">24 Jam</button>
            <button type="button" class="rsh-pill" data-range="7d">7 Hari</button>
            <button type="button" class="rsh-pill" data-range="30d">30 Hari</button>
        </div>

        {{-- Tombol Ekspor CSV --}}
        <button type="button" class="btn-rsh-csv" id="btnUnduhCsv" data-url="{{ route('riwayat-sensor.csv') }}" title="Ekspor format CSV">
            <i class="bi bi-download"></i>
            <span>Ekspor CSV</span>
        </button>

        {{-- Tombol Unduh Laporan PDF --}}
        <button type="button" class="btn-rsh-pdf" id="btnUnduhPdf" title="Unduh atau Cetak Laporan PDF">
            <i class="bi bi-file-earmark-text"></i>
            <span>Unduh Laporan PDF</span>
        </button>
    </div>
</section>

{{-- =========================================================================
     3. GRAFIK DINAMIKA PARAMETER AIR (MULTI-LINE)
     ========================================================================= --}}
<section class="riwayat-chart-card">
    <div class="rcc-head">
        <div class="rcc-info">
            <h3 class="rcc-title" id="rccTitle">Dinamika Parameter Air (24 Jam Terakhir)</h3>
            <p class="rcc-subtitle">Grafik fluktuasi multi-parameter pada {{ $selectedDevice['nama'] }}</p>
        </div>

        {{-- Custom Legend Sesuai Mockup Gambar --}}
        <div class="rcc-legend">
            <span class="rcc-legend-item"><i class="legend-dot dot-ph"></i> pH</span>
            <span class="rcc-legend-item"><i class="legend-dot dot-do"></i> DO (mg/L)</span>
            <span class="rcc-legend-item"><i class="legend-dot dot-suhu"></i> Suhu (°C)</span>
            <span class="rcc-legend-item"><i class="legend-dot dot-sal"></i> Salinitas (ppt)</span>
        </div>
    </div>

    {{-- Canvas Grafik --}}
    <div class="rcc-canvas-wrap">
        <canvas id="chartRiwayatDinamika" height="90"></canvas>
    </div>

    {{-- X-Axis Labels Footer --}}
    <div class="rcc-x-axis-labels">
        <span>Mulai Periode</span>
        <span>Tengah Periode</span>
        <span>Sekarang</span>
    </div>
</section>

{{-- =========================================================================
     4. KARTU UTAMA DATA TABEL RIWAYAT PARAMETER AIR
     ========================================================================= --}}
<section class="card-table">
    <div class="card-table-head">
        <div>
            <h2>Riwayat Parameter Air</h2>
            <p>{{ $totalEntri }} data tercatat &bull; {{ $tanggal }}</p>
        </div>

        <div class="card-head-actions">
            {{-- Filter Tanggal Minimalis --}}
            <div class="riwayat-filters-inline">
                <div class="date-box" id="boxStartDate">
                    <span class="date-tag">From</span>
                    <input type="date" value="{{ $tanggalIso }}" id="filterStartDate" aria-label="Tanggal Awal">
                    <i class="bi bi-calendar3"></i>
                </div>

                <div class="date-box" id="boxEndDate">
                    <span class="date-tag">To</span>
                    <input type="date" value="{{ $tanggalIso }}" id="filterEndDate" aria-label="Tanggal Akhir">
                    <i class="bi bi-calendar3"></i>
                </div>
            </div>

            {{-- Tombol Toggle Tampilan (Tabel vs Kartu HP) --}}
            <div class="view-toggle" id="viewToggle">
                <button type="button" class="vt-btn active" data-view="table" title="Tampilan Tabel">
                    <i class="bi bi-table"></i>
                    <span>Tabel</span>
                </button>
                <button type="button" class="vt-btn" data-view="cards" title="Tampilan Kartu (Mobile)">
                    <i class="bi bi-grid-fill"></i>
                    <span>Kartu</span>
                </button>
            </div>

            <span class="badge-entri">{{ $totalEntri }} entri</span>
        </div>
    </div>

    {{-- ================= 4.1 TAMPILAN TABEL (DESKTOP & TABLET) ================= --}}
    <div class="table-wrap" id="tableViewContainer">
        <table class="sensor-table">
            <thead>
                <tr>
                    <th class="th-waktu">Waktu & Tanggal</th>
                    <th>pH</th>
                    <th>Sal</th>
                    <th>DO</th>
                    <th>Suhu</th>
                    <th>NTU</th>
                    <th>Level</th>
                    <th>Skor</th>
                    <th>Status</th>
                    <th>Aksi Aktuator</th>
                </tr>
            </thead>
            <tbody id="sensorTableBody">
                @foreach ($readings as $index => $r)
                    <tr class="sensor-row" data-page="{{ intdiv($index, 8) }}">
                        <td class="cell-time">
                            <span class="time-main">{{ $r['waktu'] }}</span>
                            <span class="time-sub">{{ $r['tanggal_format'] }}</span>
                        </td>
                        <td><strong>{{ $r['ph'] }}</strong></td>
                        <td>{{ $r['sal'] }} <small class="text-muted">ppt</small></td>
                        <td>{{ $r['do'] }} <small class="text-muted">mg/L</small></td>
                        <td>{{ $r['suhu'] }} <small class="text-muted">°C</small></td>
                        <td>{{ $r['ntu'] }} <small class="text-muted">NTU</small></td>
                        <td>{{ $r['level'] }}%</td>
                        <td><strong>{{ $r['skor'] }}</strong></td>
                        <td>
                            <span class="badge-status {{ $r['status'] }}">
                                <i class="dot"></i>{{ strtoupper($r['status']) }}
                            </span>
                        </td>
                        <td>{{ $r['aktuator'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ================= 4.2 TAMPILAN KARTU (OPTIMAL UNTUK HP / PWA) ================= --}}
    <div class="sensor-cards-container" id="cardsViewContainer">
        @foreach ($readings as $index => $r)
            <article class="sensor-card-mobile sensor-card-item" data-page="{{ intdiv($index, 8) }}">
                <div class="scm-head">
                    <div class="scm-time-box">
                        <span class="scm-time">{{ $r['waktu'] }}</span>
                        <span class="scm-date"><i class="bi bi-calendar3"></i> {{ $r['tanggal_format'] }}</span>
                    </div>
                    <span class="badge-status {{ $r['status'] }}">
                        <i class="dot"></i>{{ strtoupper($r['status']) }}
                    </span>
                </div>

                <div class="scm-grid">
                    <div class="scm-pill">
                        <span class="scm-lbl">pH Air</span>
                        <strong class="scm-val">{{ $r['ph'] }}</strong>
                    </div>
                    <div class="scm-pill">
                        <span class="scm-lbl">Salinitas</span>
                        <strong class="scm-val">{{ $r['sal'] }} <small>ppt</small></strong>
                    </div>
                    <div class="scm-pill">
                        <span class="scm-lbl">Oksigen (DO)</span>
                        <strong class="scm-val">{{ $r['do'] }} <small>mg/L</small></strong>
                    </div>
                    <div class="scm-pill">
                        <span class="scm-lbl">Suhu Air</span>
                        <strong class="scm-val">{{ $r['suhu'] }} <small>°C</small></strong>
                    </div>
                    <div class="scm-pill">
                        <span class="scm-lbl">Kekeruhan</span>
                        <strong class="scm-val">{{ $r['ntu'] }} <small>NTU</small></strong>
                    </div>
                    <div class="scm-pill">
                        <span class="scm-lbl">Level Air</span>
                        <strong class="scm-val">{{ $r['level'] }} <small>%</small></strong>
                    </div>
                </div>

                <div class="scm-foot">
                    <div class="scm-foot-item">
                        <span class="scm-foot-lbl">Indeks Kualitas</span>
                        <strong class="scm-foot-score">{{ $r['skor'] }}</strong>
                    </div>
                    <div class="scm-foot-item">
                        <span class="scm-foot-lbl">Aksi Aktuator</span>
                        <span class="scm-foot-act">{{ $r['aktuator'] }}</span>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    {{-- ================= 4.3 FOOTER & PAGINASI ================= --}}
    <div class="table-foot">
        <span class="foot-info" id="footInfo">
            Menampilkan 1&ndash;8 dari {{ $totalEntri }} data &bull; Halaman 1 dari {{ ceil($totalEntri / 8) }}
        </span>

        <div class="riwayat-pager">
            <button class="pg-btn" id="pgPrev" aria-label="Sebelumnya">
                <i class="bi bi-chevron-left"></i>
            </button>

            @for ($p = 0; $p < ceil($totalEntri / 8); $p++)
                <button class="pg-btn {{ $p === 0 ? 'active' : '' }}" data-page="{{ $p }}">
                    {{ $p + 1 }}
                </button>
            @endfor

            <button class="pg-btn" id="pgNext" aria-label="Berikutnya">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

@endsection

@push('scripts')
{{-- Kirim payload data grafik dari controller ke frontend --}}
<script>
    window.aquacrabChartPayload = @json($chartData ?? []);
</script>
<script src="{{ asset('js/riwayat-sensor.js') }}"></script>
@endpush
