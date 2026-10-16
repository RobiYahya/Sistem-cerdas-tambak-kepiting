@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $statusLabel = ['normal' => 'NORMAL', 'cukup' => 'CUKUP', 'waspada' => 'WASPADA', 'kritis' => 'KRITIS'];
    // Lingkar gauge: r = 100 -> keliling = 2 * pi * 100
    $keliling = 2 * M_PI * 100;
    $isi      = $keliling * ($kualitas['indeks'] / 100);
@endphp

@section('content')
<header class="page-head">
    <h1>Monitoring Kualitas Air Laut</h1>
    <p>Dashboard utama untuk pemantauan parameter air dan notifikasi sistem.</p>
</header>

<div class="grid">

    {{-- ================= KARTU STATUS KUALITAS AIR ================= --}}
    <section class="card-status">
        <div class="status-left">
            <div class="status-top">
                <div>
                    <h2>Status Kualitas Air</h2>
                    <p>Indeks kualitas air berdasarkan data terbaru.</p>
                </div>
            </div>

            <div class="status-index">
                <span class="lbl">Nilai indeks</span>
                <strong>{{ number_format($kualitas['indeks'], 1, ',', '.') }}</strong>
            </div>

            <div class="sub-index">
                @foreach ($kualitas['sub'] as $s)
                    <div class="sub-box">
                        <span>{{ $s['label'] }}</span>
                        <strong>{{ $s['nilai'] }}%</strong>
                    </div>
                @endforeach
            </div>
        </div>

        <span class="pill-baik"><i class="dot"></i>{{ $kualitas['status'] }}</span>

        <div class="gauge">
            <svg viewBox="0 0 240 240">
                <circle cx="120" cy="120" r="100" class="gauge-bg"/>
                <circle cx="120" cy="120" r="100" class="gauge-fg"
                        stroke-dasharray="{{ $isi }} {{ $keliling }}"
                        transform="rotate(-90 120 120)"/>
            </svg>
            <div class="gauge-text">
                <strong>{{ number_format($kualitas['indeks'], 1, ',', '.') }}</strong>
                <span>Indeks Kualitas</span>
            </div>
        </div>
    </section>

    {{-- ================= PARAMETER AIR (KARTU GESER) ================= --}}
    <section class="param-panel">
        <div class="panel-head">
            <div>
                <h2>Parameter Air</h2>
                <p>6 kartu sensor dalam tampilan geser</p>
            </div>
            <div class="arrows">
                <button class="arrow" id="paramPrev" aria-label="Sebelumnya"><i class="bi bi-chevron-left"></i></button>
                <button class="arrow" id="paramNext" aria-label="Berikutnya"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>

        @foreach ($parameters as $i => $p)
            <article class="param-card" data-page="{{ intdiv($i, 2) }}">
                <div class="param-top">
                    <h3>{{ $p['nama'] }}</h3>
                    <span class="badge {{ $p['status'] }}"><i class="dot"></i>{{ $statusLabel[$p['status']] }}</span>
                </div>
                <div class="param-value">{{ $p['nilai'] }}<small>{{ $p['satuan'] }}</small></div>
                <div class="bar"><i class="{{ $p['status'] }}" style="width: {{ $p['persen'] }}%"></i></div>
            </article>
        @endforeach

        <div class="dots" id="paramDots">
            @for ($d = 0; $d < ceil(count($parameters) / 2); $d++)
                <i class="{{ $d === 0 ? 'on' : '' }}"></i>
            @endfor
        </div>
    </section>

    {{-- ================= TREN ENAM PARAMETER ================= --}}
    <section class="card-trend">
        <div class="trend-head">
            <div>
                <h2>Tren Enam Parameter</h2>
                <p>24 jam terakhir • 6 parameter utama</p>
            </div>
            <div class="ranges" id="ranges">
                <button class="chip active">24 jam</button>
                <button class="chip">7 hari</button>
                <button class="chip">30 hari</button>
            </div>
        </div>

        <div class="trend-grid">
            @foreach ($trends as $i => $t)
                <article class="trend-card" data-page="{{ intdiv($i, 4) }}">
                    <div class="trend-top">
                        <div>
                            <span class="trend-name">{{ $t['nama'] }}</span>
                            <div class="trend-val">{{ $t['nilai'] }}</div>
                        </div>
                        <span class="badge {{ $t['status'] }}"><i class="dot"></i>{{ $statusLabel[$t['status']] }}</span>
                    </div>
                    <div class="chart-wrap"><canvas id="chart-{{ $t['id'] }}"></canvas></div>
                </article>
            @endforeach
        </div>

        <div class="trend-foot">
            <span id="pageInfo">Halaman 1 dari 2</span>
            <div class="pager">
                <button class="pg" id="pgPrev" aria-label="Sebelumnya"><i class="bi bi-chevron-left"></i></button>
                <button class="pg active" data-go="0">1</button>
                <button class="pg" data-go="1">2</button>
                <button class="pg" id="pgNext" aria-label="Berikutnya"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </section>

    {{-- ================= NOTIFIKASI TERKINI ================= --}}
    <section class="card-notif">
        <div class="notif-head">
            <div>
                <h2>Notifikasi Terkini</h2>
                <p>Riwayat alert sistem terbaru.</p>
            </div>
            <span class="alert-count"><i class="bi bi-exclamation-circle"></i>{{ count($alerts) }} alert</span>
        </div>

        <div class="notif-list">
            @foreach ($alerts as $a)
                <div class="notif-item">
                    <span class="notif-icon"><i class="bi bi-exclamation-circle"></i></span>
                    <div class="notif-body">
                        <div class="notif-row">
                            <strong>{{ $a['tipe'] }}</strong>
                            <span>{{ $a['lokasi'] }}</span>
                        </div>
                        <p>{{ $a['pesan'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
    // Data dummy dikirim dari controller -> dipakai oleh public/js/dashboard.js
    window.TRENDS = @json($trends);
</script>
<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush