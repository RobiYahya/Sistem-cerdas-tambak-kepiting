@extends('layouts.app')

@section('title', 'Inferensi Fuzzy')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/inferensi-fuzzy.css') }}">
@endpush

@section('content')
{{-- ===== PAGE HEADER ===== --}}
<div class="fuzzy-head">
    <div class="fuzzy-title-group">
        <h1>Inferensi Fuzzy</h1>
        <p>Fuzzifikasi, indeks sub sistem, aturan aktif, dan hasil agregasi.</p>
    </div>

    {{-- Dropdown Unit --}}
    <div class="unit-selector-wrapper">
        <button class="unit-selector-btn" id="unitDropdownBtn" type="button" aria-haspopup="true" aria-expanded="false">
            <span id="selectedUnitText">{{ $unitAktif }}</span>
            <i class="bi bi-chevron-down"></i>
        </button>
        <div class="unit-selector-dropdown" id="unitDropdownMenu">
            @foreach ($units as $unit)
                <div class="unit-option {{ $unit === $unitAktif ? 'selected' : '' }}" data-unit="{{ $unit }}">
                    <span>{{ $unit }}</span>
                    @if ($unit === $unitAktif)
                        <i class="bi bi-check2"></i>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ===== MAIN GRID (2 KOLOM) ===== --}}
<div class="fuzzy-layout">

    {{-- ================= KOLOM KIRI ================= --}}
    <div class="fuzzy-col">

        {{-- ===== TAHAP 1: FUZZIFIKASI ===== --}}
        <section class="fuzzy-card">
            <div class="fuzzy-card-header">
                <h2>Tahap 1 - Fuzzifikasi</h2>
                <p>Nilai masukan dan hasil fuzzifikasi untuk setiap parameter.</p>
            </div>

            <div class="param-grid">
                @foreach ($fuzzifikasi as $p)
                    <article class="param-item">
                        <span class="param-label">{{ $p['parameter'] }}</span>
                        <div class="param-val">
                            {{ $p['nilai'] }}@if (!empty($p['satuan']))@if ($p['satuan'] === '%')<span style="font-size: 17px; font-weight: 600;">%</span>@else<span style="font-size: 14.5px; font-weight: 500; margin-left: 3px;">{{ $p['satuan'] }}</span>@endif @endif
                        </div>
                        <span class="param-fuzzy">{{ $p['hasil'] }}</span>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- ===== TAHAP 2: INDEKS SUB SISTEM ===== --}}
        <section class="fuzzy-card">
            <div class="fuzzy-card-header">
                <h2>Tahap 2 - Indeks Sub Sistem</h2>
                <p>Indeks kimia, osmotik, dan fisik.</p>
            </div>

            <div class="subsistem-list">
                @foreach ($subSistem as $s)
                    <div class="subsistem-item">
                        <div class="sub-meta-top">
                            <span class="sub-code">{{ $s['subtitle'] }}</span>
                            <span class="sub-val-top">{{ $s['format'] }}</span>
                        </div>
                        <h3 class="sub-title">{{ $s['nama'] }}</h3>
                        <div class="sub-progress-bar">
                            <div class="sub-progress-fill" style="width: {{ $s['nilai'] }}%;"></div>
                        </div>
                        <div class="sub-meta-bottom">
                            <span class="sub-val-bottom">{{ $s['format'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

    </div>

    {{-- ================= KOLOM KANAN ================= --}}
    <div class="fuzzy-col">

        {{-- ===== TAHAP 3: ATURAN YANG AKTIF ===== --}}
        <section class="fuzzy-card">
            <div class="fuzzy-card-header">
                <h2>Tahap 3 - Aturan yang Aktif</h2>
                <p>Aturan fuzzy aktif berdasarkan kondisi masukan.</p>
            </div>

            <div class="rules-list">
                @foreach ($aturanAktif as $r)
                    <article class="rule-item">
                        <div class="rule-top">
                            <div class="rule-top-left">
                                <span class="rule-code">{{ $r['id'] }}</span>
                                <span class="rule-alpha">{{ $r['alpha'] }}</span>
                            </div>
                            <span class="rule-status">{{ $r['status'] }}</span>
                        </div>
                        <div class="rule-desc">
                            {{ $r['teks'] }}
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- ===== TAHAP 4: AGREGASI DAN CENTROID ===== --}}
        <section class="fuzzy-card">
            <div class="fuzzy-card-header">
                <h2>Tahap 4 - Agregasi dan Centroid</h2>
                <p>Hasil agregasi fuzzy dan centroid.</p>
            </div>

            <div class="chart-box">
                <svg class="fuzzy-svg-chart" viewBox="0 0 540 235" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        {{-- Gradient fill untuk area fuzzy --}}
                        <linearGradient id="fuzzyAreaGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#86efac" stop-opacity="0.65"/>
                            <stop offset="100%" stop-color="#bbf7d0" stop-opacity="0.4"/>
                        </linearGradient>
                    </defs>

                    {{-- Area Bawah Kurva Fuzzy (Polygon) --}}
                    <polygon points="
                        40,195 
                        132,195 
                        160,183 
                        270,183 
                        311,132 
                        426,132 
                        454,85 
                        498,85 
                        498,195
                    " fill="url(#fuzzyAreaGrad)"/>

                    {{-- Garis Stroke Kurva Fuzzy --}}
                    <polyline points="
                        40,195 
                        132,195 
                        160,183 
                        270,183 
                        311,132 
                        426,132 
                        454,85 
                        498,85 
                        498,195
                    " fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linejoin="round" stroke-linecap="round"/>

                    {{-- Sumbu Y (Kiri) --}}
                    <line x1="40" y1="20" x2="40" y2="195" stroke="#374151" stroke-width="1.8" stroke-linecap="square"/>

                    {{-- Sumbu X (Bawah) --}}
                    <line x1="40" y1="195" x2="505" y2="195" stroke="#374151" stroke-width="1.8" stroke-linecap="square"/>

                    {{-- Label Sumbu X --}}
                    <text x="40" y="220" font-family="'Poppins', sans-serif" font-size="13" font-weight="500" fill="#4b5563" text-anchor="middle">0</text>
                    <text x="155" y="220" font-family="'Poppins', sans-serif" font-size="13" font-weight="500" fill="#4b5563" text-anchor="middle">25</text>
                    <text x="270" y="220" font-family="'Poppins', sans-serif" font-size="13" font-weight="500" fill="#4b5563" text-anchor="middle">50</text>
                    <text x="385" y="220" font-family="'Poppins', sans-serif" font-size="13" font-weight="500" fill="#4b5563" text-anchor="middle">75</text>
                    <text x="498" y="220" font-family="'Poppins', sans-serif" font-size="13" font-weight="500" fill="#4b5563" text-anchor="middle">100</text>
                </svg>
            </div>

            {{-- Centroid Result Pill (Navigasi ke Kontrol Aktuator) --}}
            <div class="centroid-pill-wrapper">
                <a href="{{ route('kontrol-aktuator') }}" class="centroid-pill" title="Lihat Kontrol Aktuator">
                    <span class="skor-label">Skor {{ $agregasi['format'] }}</span>
                    <span class="skor-status">{{ $agregasi['status'] }}</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </section>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropdownBtn = document.getElementById('unitDropdownBtn');
        const dropdownMenu = document.getElementById('unitDropdownMenu');
        const selectedText = document.getElementById('selectedUnitText');
        const options = document.querySelectorAll('.unit-option');

        if (dropdownBtn && dropdownMenu) {
            dropdownBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });

            options.forEach(opt => {
                opt.addEventListener('click', function () {
                    const unit = this.getAttribute('data-unit');
                    selectedText.textContent = unit;
                    options.forEach(o => o.classList.remove('selected'));
                    this.classList.add('selected');
                    dropdownMenu.classList.remove('show');
                });
            });

            document.addEventListener('click', function () {
                dropdownMenu.classList.remove('show');
            });
        }
    });
</script>
@endpush
