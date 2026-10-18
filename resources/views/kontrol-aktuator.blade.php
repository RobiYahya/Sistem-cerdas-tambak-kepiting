@extends('layouts.app')

@section('title', 'Kontrol Aktuator')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/kontrol-aktuator.css') }}">
@endpush

@section('content')
{{-- ===== HEADER ===== --}}
<div class="page-head">
    <h1>Kontrol Aktuator</h1>
    <p>Pengaturan otomatis dan status aktuator kolam.</p>
</div>

{{-- ===== STATUS KUALITAS AIR ===== --}}
<div class="card-status" style="margin-top:26px;min-height:unset;">
    <span class="pill-baik">
        <span class="dot"></span>{{ $kualitas['status'] }}
    </span>

    <div class="status-left">
        <div class="status-top">
            <h2>Status Kualitas Air</h2>
            <p>Indeks kualitas air berdasarkan data terbaru.</p>
        </div>

        <div class="status-index">
            <span class="lbl">Nilai Indeks</span>
            <strong>{{ number_format($kualitas['indeks'], 1, ',', '.') }}</strong>
        </div>

        <div class="sub-index">
            <div class="sub-box">
                <span>Indeks Kimiawi</span>
                <strong>{{ $kualitas['kimia'] }}%</strong>
            </div>
            <div class="sub-box">
                <span>Indeks Osmotik</span>
                <strong>{{ $kualitas['osmotik'] }}%</strong>
            </div>
            <div class="sub-box">
                <span>Indeks Fisik</span>
                <strong>{{ $kualitas['fisik'] }}%</strong>
            </div>
        </div>
    </div>

    <div class="gauge">
        <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
            <circle cx="60" cy="60" r="48" fill="none"
                stroke="rgba(255,255,255,0.15)" stroke-width="14"/>
            <circle cx="60" cy="60" r="48" fill="none"
                stroke="#d9fb5c" stroke-width="14" stroke-linecap="round"
                stroke-dasharray="{{ round(2*3.14159*48*$kualitas['indeks']/100,2) }} {{ round(2*3.14159*48,2) }}"
                stroke-dashoffset="{{ round(2*3.14159*48*0.25,2) }}"
                transform="rotate(-90 60 60)"/>
        </svg>
        <div class="gauge-text">
            <strong>{{ number_format($kualitas['indeks'], 1, ',', '.') }}</strong>
            <span>Indeks Kualitas</span>
        </div>
    </div>
</div>

{{-- ===== PARAMETER SENSOR ===== --}}
<div class="ka-param-grid">
    @foreach ($parameter as $p)
    <div class="param-card" style="margin-bottom:0;">
        <div class="param-top">
            <h3 style="font-weight:500;color:var(--muted);">{{ $p['label'] }}</h3>
            <span class="badge {{ $p['status'] }}">
                <span class="dot"></span>{{ $p['label_status'] }}
            </span>
        </div>
        <div class="param-value">
            {{ is_float($p['nilai']) ? number_format($p['nilai'], 2, ',', '.') : $p['nilai'] }}<small>{{ $p['satuan'] }}</small>
        </div>
        <div class="bar">
            <i class="{{ $p['status'] }}" style="width:{{ $p['persen'] }}%"></i>
        </div>
    </div>
    @endforeach
</div>

{{-- ===== STATUS ALAT ===== --}}
<div class="ka-alat-section">
    <div class="ka-alat-head">
        <h2>Status Alat</h2>
        <label class="ka-mode-toggle" id="modeToggleLabel" title="Mode Otomatis Fuzzy">
            <span>Mode Otomatis (Fuzzy)</span>
            <div class="ka-toggle-switch">
                <input type="checkbox" id="modeCheckbox" {{ $modeOtomatis ? 'checked' : '' }}>
                <span class="ka-toggle-track">
                    <span class="ka-toggle-knob"></span>
                    <span class="ka-toggle-on">ON</span>
                    <span class="ka-toggle-off">OFF</span>
                </span>
            </div>
        </label>
    </div>

    <div class="ka-alat-grid">
        @foreach ($aktuator as $alat)
        <div class="ka-alat-card" id="card-{{ $alat['id'] }}">
            <div>
                <h3>{{ $alat['nama'] }}</h3>
                <p style="font-size:13px;color:var(--muted);margin-top:4px;">
                    Status : <span id="status-{{ $alat['id'] }}">{{ $alat['status'] }}</span>
                </p>
                <p style="font-size:12.5px;color:var(--muted);margin-top:2px;">
                    Kondisi : {{ $alat['kondisi'] }}
                </p>
            </div>
            <label class="ka-device-toggle" title="{{ $alat['nama'] }}">
                <input type="checkbox" class="ka-device-checkbox"
                       id="toggle-{{ $alat['id'] }}"
                       data-id="{{ $alat['id'] }}"
                       {{ $alat['aktif'] ? 'checked' : '' }}>
                <span class="ka-device-track">
                    <span class="ka-device-knob"></span>
                    <span class="ka-toggle-on">ON</span>
                    <span class="ka-toggle-off">OFF</span>
                </span>
            </label>
        </div>
        @endforeach
    </div>
</div>

{{-- ===== MODAL KONFIRMASI GANTI MODE MANUAL ===== --}}
<div class="ka-modal-backdrop" id="modalBackdrop" aria-hidden="true">
    <div class="ka-modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="ka-modal-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <h3 id="modalTitle">Ganti ke Mode Manual?</h3>
        <p>Sistem tidak lagi dikontrol otomatis oleh algoritma Fuzzy. Anda harus mengatur setiap aktuator secara manual.</p>
        <div class="ka-modal-actions">
            <button class="ka-btn-cancel" id="btnBatalModal">Batal</button>
            <button class="ka-btn-confirm" id="btnKonfirmasiManual">Ya, Ganti Manual</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/kontrol-aktuator.js') }}"></script>
@endpush