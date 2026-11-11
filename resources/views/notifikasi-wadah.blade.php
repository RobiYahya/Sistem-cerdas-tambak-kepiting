@extends('layouts.app')

@section('title', 'Notifikasi Wadah')

@section('content')

{{-- =========================================================================
     1. HEADER UTAMA — KONSISTEN DENGAN DASHBOARD
     ========================================================================= --}}
<header class="page-head">

    {{-- Breadcrumb --}}
    <div class="wadah-detail-breadcrumb">
        <a href="{{ route('dashboard') }}" class="wdb-link">Perangkat Saya</a>
        <i class="bi bi-chevron-right wdb-sep"></i>
        <span class="wdb-device">{{ Str::limit($selectedDevice['nama'] ?? 'Unit 01', 28) }}</span>
        <i class="bi bi-chevron-right wdb-sep"></i>
        <span class="wdb-current">Pusat Notifikasi &amp; Peringatan</span>
    </div>

    {{-- Title Bar --}}
    <div class="page-head-bar wadah-head-bar">
        <div class="header-wadah-title-wrap">
            <h1 class="wadah-title-detail">
                Pusat Notifikasi &amp; Peringatan &mdash; {{ $selectedDevice['nama'] ?? 'Unit 01 — Apartemen Kepiting Soka A1' }}
                <span class="badge-role-pill">{{ $isTeknisi ? 'TEKNISI' : 'NELAYAN' }}</span>
                <span class="combined-telemetry-pill">
                    <span class="ctp-item"><i class="bi bi-wifi"></i> {{ $selectedDevice['rssi'] ?? '-68 dBm' }}</span>
                    <span class="ctp-sep">&bull;</span>
                    <span class="ctp-item"><i class="bi bi-battery-charging"></i> {{ $selectedDevice['baterai'] ?? '94%' }}</span>
                </span>
            </h1>
            <p class="wadah-subtitle-detail">Peringatan terstruktur berbasis prioritas Kritis, Tinggi, Sedang, dan Rendah yang dipicu oleh sensor wadah.</p>
        </div>

        <div class="wadah-head-actions">
            {{-- Bell Notifikasi --}}
            <a href="{{ route('notifikasi-wadah') }}" class="btn-notif-bell btn-notif-bell-active" title="Pusat Notifikasi">
                <i class="bi bi-bell-fill"></i>
                <span class="notif-badge-dot" id="bellCounter">{{ $stats['butuh_tindakan'] ?? 2 }}</span>
            </a>

            {{-- Device Selector Pill --}}
            <div class="header-device-selector-pill">
                <i class="bi bi-stack hdsp-icon"></i>
                <span class="hdsp-name">{{ Str::limit($selectedDevice['nama'] ?? 'Unit 01', 22) }}</span>
                <span class="hdsp-badge">{{ $selectedDevice['kualitas'] ?? 'BAIK' }}</span>
                <i class="bi bi-chevron-down hdsp-chevron"></i>
                <select class="hdsp-select" onchange="window.location.href='{{ route('notifikasi-wadah') }}?device=' + this.value" title="Ganti Wadah">
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
     2. HERO CARD — RINGKASAN PUSAT NOTIFIKASI
     ========================================================================= --}}
<section class="notif-hero-card">
    <div class="nhc-left">
        <div class="nhc-title-row">
            <h3>Pusat Notifikasi &amp; Peringatan Tambak</h3>
            <span class="badge-butuh-tindakan" id="actionCounter">{{ $stats['butuh_tindakan'] ?? 2 }} Butuh Tindakan</span>
        </div>
        <p>Log peringatan real-time yang dipicu oleh sensor kualitas air atau status perangkat telemetri.</p>
    </div>

    <button type="button" class="btn-tandai-semua" id="btnMarkAllDone">
        <i class="bi bi-check2-all"></i>
        <span>Tandai Semua Selesai</span>
    </button>
</section>

{{-- =========================================================================
     3. 4 STAT CARDS PRIORITAS
     ========================================================================= --}}
<section class="notif-stats-grid">
    <div class="nstat-card nstat-kritis">
        <div class="nstat-icon-wrap"><i class="bi bi-exclamation-octagon-fill"></i></div>
        <span class="nstat-label">Kritis (Critical)</span>
        <div class="nstat-val-row">
            <strong class="nstat-num text-red">{{ $stats['kritis'] ?? 1 }}</strong>
            <span class="nstat-unit">Peringatan</span>
        </div>
    </div>

    <div class="nstat-card nstat-tinggi">
        <div class="nstat-icon-wrap"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <span class="nstat-label">Tinggi (High)</span>
        <div class="nstat-val-row">
            <strong class="nstat-num text-orange">{{ $stats['tinggi'] ?? 1 }}</strong>
            <span class="nstat-unit">Peringatan</span>
        </div>
    </div>

    <div class="nstat-card nstat-sedang">
        <div class="nstat-icon-wrap"><i class="bi bi-info-circle-fill"></i></div>
        <span class="nstat-label">Sedang (Medium)</span>
        <div class="nstat-val-row">
            <strong class="nstat-num text-blue">{{ $stats['sedang'] ?? 1 }}</strong>
            <span class="nstat-unit">Peringatan</span>
        </div>
    </div>

    <div class="nstat-card nstat-rendah">
        <div class="nstat-icon-wrap"><i class="bi bi-bell-fill"></i></div>
        <span class="nstat-label">Rendah / Info (Low)</span>
        <div class="nstat-val-row">
            <strong class="nstat-num text-slate">{{ $stats['rendah'] ?? 1 }}</strong>
            <span class="nstat-unit">Peringatan</span>
        </div>
    </div>
</section>

{{-- =========================================================================
     4. TOOLBAR PENCARIAN & FILTER PRIORITAS
     ========================================================================= --}}
<section class="notif-toolbar-card">
    <div class="notif-search-wrap">
        <i class="bi bi-search"></i>
        <input type="text" id="notifSearchInput" class="notif-search-input"
               placeholder="Cari teks notifikasi atau wadah...">
    </div>

    <div class="notif-filter-tabs" id="notifFilterTabs">
        <button type="button" class="nft-tab-btn active" data-filter="all">Semua Prioritas</button>
        <button type="button" class="nft-tab-btn" data-filter="kritis">Kritis</button>
        <button type="button" class="nft-tab-btn" data-filter="tinggi">Tinggi</button>
        <button type="button" class="nft-tab-btn" data-filter="sedang">Sedang</button>
        <button type="button" class="nft-tab-btn" data-filter="rendah">Rendah</button>
    </div>
</section>

{{-- =========================================================================
     5. DAFTAR KARTU NOTIFIKASI
     ========================================================================= --}}
<div class="notif-cards-list" id="notifCardsList">
    @foreach ($notifikasiList as $item)
        <article class="notif-item-card {{ $item['card_class'] }}"
                 data-id="{{ $item['id'] }}"
                 data-level="{{ $item['level'] }}"
                 data-status="{{ $item['status'] }}"
                 data-title="{{ strtolower($item['judul']) }}"
                 data-desc="{{ strtolower($item['deskripsi']) }}"
                 data-wadah="{{ strtolower($item['wadah']) }}">

            {{-- Kolom Kiri: Info Notifikasi --}}
            <div class="nic-left">
                <div class="nic-top-meta">
                    <span class="nic-priority-badge {{ $item['badge_class'] }}">{{ $item['badge'] }}</span>
                    <strong class="nic-device-name">{{ $item['wadah'] }}</strong>
                    <span class="nic-time-sep">&middot;</span>
                    <span class="nic-time-ago">{{ $item['waktu'] }}</span>
                </div>

                <h4 class="nic-title">{{ $item['judul'] }}</h4>

                <div class="nic-trigger-box">
                    <span class="nic-trigger-lbl">Nilai Pemicu:</span>
                    <code class="nic-trigger-val">{{ $item['pemicu'] }}</code>
                </div>

                <p class="nic-desc">{{ $item['deskripsi'] }}</p>

                @if ($item['konfirmasi'])
                    <div class="nic-confirmed-note">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ $item['konfirmasi'] }}</span>
                    </div>
                @endif
            </div>

            {{-- Kolom Kanan: Tombol Aksi --}}
            <div class="nic-right">
                @if ($item['status'] === 'pending')
                    <button type="button"
                            class="btn-confirm-action btn-trigger-confirm"
                            data-id="{{ $item['id'] }}"
                            title="Konfirmasi tindakan alert ini">
                        Konfirmasi Tindakan
                    </button>
                @else
                    <button type="button" class="btn-done-action" disabled>
                        <i class="bi bi-check-circle"></i> Selesai
                    </button>
                @endif
            </div>
        </article>
    @endforeach
</div>

{{-- Pesan Kosong --}}
<div id="noNotifFound" style="display: none; background: #ffffff; border: 1px dashed var(--line); border-radius: 18px; padding: 48px 24px; text-align: center; margin-top: 16px;">
    <i class="bi bi-bell-slash" style="font-size: 32px; color: #94a3b8; display: block; margin-bottom: 12px;"></i>
    <h4 style="font-size: 16px; font-weight: 700; color: var(--ink); margin-bottom: 6px;">Tidak ada notifikasi</h4>
    <p style="font-size: 13.5px; color: var(--muted);">Tidak ada log notifikasi yang cocok dengan filter atau kata kunci pencarian.</p>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput   = document.getElementById('notifSearchInput');
    const filterTabs    = document.querySelectorAll('#notifFilterTabs .nft-tab-btn');
    const cards         = document.querySelectorAll('.notif-item-card');
    const noFound       = document.getElementById('noNotifFound');
    const actionCounter = document.getElementById('actionCounter');
    const bellCounter   = document.getElementById('bellCounter');
    const btnMarkAll    = document.getElementById('btnMarkAllDone');

    let currentFilter = 'all';
    let searchQuery   = '';

    // ───── Filter + Search ─────
    function applyFilters() {
        let visible = 0;
        cards.forEach(card => {
            const level  = card.dataset.level  || '';
            const title  = card.dataset.title  || '';
            const desc   = card.dataset.desc   || '';
            const wadah  = card.dataset.wadah  || '';

            const matchFilter = currentFilter === 'all' || level === currentFilter;
            const matchSearch = !searchQuery
                || title.includes(searchQuery)
                || desc.includes(searchQuery)
                || wadah.includes(searchQuery);

            if (matchFilter && matchSearch) {
                card.style.display = '';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });
        if (noFound) noFound.style.display = visible === 0 ? 'block' : 'none';
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            searchQuery = this.value.trim().toLowerCase();
            applyFilters();
        });
    }

    filterTabs.forEach(btn => {
        btn.addEventListener('click', function () {
            filterTabs.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            applyFilters();
        });
    });

    // ───── Hitung pending dan perbarui counter ─────
    function recountPending() {
        const pendingCount = document.querySelectorAll('.notif-item-card[data-status="pending"]').length;
        if (actionCounter) {
            actionCounter.textContent = pendingCount + ' Butuh Tindakan';
            if (pendingCount === 0) {
                actionCounter.style.background = '#dcfce7';
                actionCounter.style.color = '#15803d';
            }
        }
        if (bellCounter) {
            bellCounter.textContent = pendingCount;
            bellCounter.style.display = pendingCount === 0 ? 'none' : '';
        }
    }

    // ───── Konfirmasi per Item ─────
    function confirmCard(card, btn) {
        if (!card || card.dataset.status === 'confirmed') return;

        card.dataset.status = 'confirmed';

        // Ganti tombol → Selesai
        btn.className = 'btn-done-action';
        btn.disabled  = true;
        btn.innerHTML = '<i class="bi bi-check-circle"></i> Selesai';

        // Tambahkan catatan konfirmasi jika belum ada
        const leftCol = card.querySelector('.nic-left');
        if (leftCol && !leftCol.querySelector('.nic-confirmed-note')) {
            const note = document.createElement('div');
            note.className = 'nic-confirmed-note';
            note.innerHTML = '<i class="bi bi-check-circle-fill"></i> <span>Telah dikonfirmasi baru saja</span>';
            leftCol.appendChild(note);
        }

        recountPending();
    }

    // Pasang event listener ke semua tombol konfirmasi
    document.querySelectorAll('.btn-trigger-confirm').forEach(btn => {
        btn.addEventListener('click', function () {
            confirmCard(this.closest('.notif-item-card'), this);
        });
    });

    // ───── Tandai Semua Selesai ─────
    if (btnMarkAll) {
        btnMarkAll.addEventListener('click', function () {
            document.querySelectorAll('.notif-item-card[data-status="pending"] .btn-trigger-confirm').forEach(btn => {
                confirmCard(btn.closest('.notif-item-card'), btn);
            });
        });
    }
});
</script>
@endpush
