@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
{{-- ===== HEADER HALAMAN ===== --}}
<header class="page-head">
    <h1>Monitoring Kualitas Air Laut</h1>
    <p>Hubungkan perangkat untuk memulai pemantauan kualitas air tambak kepiting.</p>
</header>

{{-- ===== SECTION: PERANGKAT TAMBAK KEPITING ===== --}}
<div class="empty-section-head">
    <h2>Perangkat Tambak Kepiting</h2>
    <span class="badge-device-count">
        <i class="dot"></i> 0 perangkat
    </span>
</div>

{{-- ===== KARTU UTAMA EMPTY STATE ===== --}}
<section class="card-empty-device">
    @include('partials.crab-onboarding')

    <h3>Belum ada perangkat tambak kepiting</h3>
    <p>Tambahkan perangkat untuk memantau enam parameter air: pH, oksigen terlarut, suhu air, level air, salinitas, dan kekeruhan.</p>

    <button type="button" class="btn-primary-lime" id="btnBukaModal">
        <i class="bi bi-plus-lg"></i>
        <span>Tambahkan perangkat pertama</span>
    </button>
</section>

{{-- ===== SECTION: TIGA LANGKAH UNTUK MULAI ===== --}}
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

@include('partials.modal-tambah-perangkat')
@endsection

