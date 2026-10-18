@extends('layouts.app')

@section('title', 'Riwayat Sensor')

@section('content')
<header class="page-head">
    <h1>Riwayat Sensor</h1>
    <p>Tabel riwayat parameter air dan status aktuator.</p>
</header>

{{-- ================= FILTER TANGGAL & UNDUH CSV (MINIMALIS & SEJAJAR) ================= --}}
<div class="riwayat-filters">
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

    <button type="button" class="btn-unduh-csv" id="btnUnduhCsv" data-url="{{ route('riwayat-sensor.csv') }}" title="Unduh format CSV">
        <i class="bi bi-download"></i>
        <span class="btn-text-full">Unduh CSV</span>
        <span class="btn-text-short">CSV</span>
    </button>
</div>

{{-- ================= KARTU UTAMA DATA RIWAYAT ================= --}}
<section class="card-table">
    <div class="card-table-head">
        <div>
            <h2>Riwayat Parameter Air</h2>
            <p>{{ $totalEntri }} data tercatat • {{ $tanggal }}</p>
        </div>

        <div class="card-head-actions">
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

    {{-- ================= 1. TAMPILAN TABEL (DESKTOP & TABLET) ================= --}}
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
                    <tr class="sensor-row" data-page="{{ intdiv($index, 12) }}">
                        <td class="cell-time">
                            <span class="time-main">{{ $r['waktu'] }}</span>
                            <span class="time-sub">{{ $r['tanggal_format'] }}</span>
                        </td>
                        <td>{{ $r['ph'] }}</td>
                        <td>{{ $r['sal'] }}</td>
                        <td>{{ $r['do'] }}</td>
                        <td>{{ $r['suhu'] }}</td>
                        <td>{{ $r['ntu'] }}</td>
                        <td>{{ $r['level'] }}</td>
                        <td>{{ $r['skor'] }}</td>
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

    {{-- ================= 2. TAMPILAN KARTU (OPTIMAL UNTUK HP / PWA) ================= --}}
    <div class="sensor-cards-container" id="cardsViewContainer">
        @foreach ($readings as $index => $r)
            <article class="sensor-card-mobile sensor-card-item" data-page="{{ intdiv($index, 12) }}">
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

    {{-- ================= FOOTER & PAGINASI ================= --}}
    <div class="table-foot">
        <span class="foot-info" id="footInfo">
            Menampilkan 1–12 dari {{ $totalEntri }} data • Halaman 1 dari {{ ceil($totalEntri / 12) }}
        </span>

        <div class="riwayat-pager">
            <button class="pg-btn" id="pgPrev" aria-label="Sebelumnya">
                <i class="bi bi-chevron-left"></i>
            </button>

            @for ($p = 0; $p < ceil($totalEntri / 12); $p++)
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
<script src="{{ asset('js/riwayat-sensor.js') }}"></script>
@endpush
