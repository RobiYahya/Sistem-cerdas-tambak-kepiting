<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kualitas Air - {{ $device['nama'] }} ({{ $startDate }} s.d. {{ $endDate }})</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --ink: #0f172a;
            --muted: #475569;
            --line: #cbd5e1;
            --forest: #0d2b1a;
            --forest-light: #15452a;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f1f5f9;
            color: var(--ink);
            line-height: 1.45;
            padding-bottom: 60px;
        }

        /* ── BAR AKSI ATAS (TIDAK DICETAK) ── */
        .pdf-top-actions {
            background: #0f172a;
            color: #ffffff;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        .pta-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .pta-back {
            color: #cbd5e1;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            background: rgba(255,255,255,0.08);
            transition: all .15s ease;
        }
        .pta-back:hover {
            color: #ffffff;
            background: rgba(255,255,255,0.15);
        }
        .pta-title {
            font-size: 14px;
            font-weight: 600;
            color: #e2e8f0;
        }
        .pta-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .pta-hint {
            font-size: 12px;
            color: #94a3b8;
        }
        .btn-print-now {
            background: #22c55e;
            color: #052e16;
            border: none;
            border-radius: 10px;
            padding: 9px 20px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all .15s ease;
        }
        .btn-print-now:hover {
            background: #16a34a;
            color: #ffffff;
        }

        /* ── DOKUMEN LAPORAN RESMI (A4 FORMAT) ── */
        .pdf-document {
            max-width: 960px;
            margin: 32px auto;
            background: #ffffff;
            padding: 48px 56px;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            border: 1px solid var(--line);
        }

        /* Kop Laporan */
        .doc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2.5px solid var(--ink);
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .doc-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .doc-logo {
            width: 52px;
            height: 52px;
            background: var(--forest);
            color: #d9fb5c;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 26px;
            font-weight: 800;
        }
        .doc-company {
            font-size: 19px;
            font-weight: 800;
            color: var(--forest);
            letter-spacing: -0.3px;
        }
        .doc-sub {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .doc-meta-right {
            text-align: right;
        }
        .doc-code {
            font-size: 11.5px;
            font-family: monospace;
            background: #f1f5f9;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 4px;
        }
        .doc-date {
            font-size: 11.5px;
            color: #64748b;
        }

        /* Judul Laporan */
        .doc-title-box {
            text-align: center;
            margin-bottom: 28px;
        }
        .doc-title-box h1 {
            font-size: 18px;
            font-weight: 800;
            color: var(--ink);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
        }
        .doc-title-box p {
            font-size: 13px;
            color: #475569;
            font-weight: 500;
        }

        /* Info Grid Dua Kolom */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            font-size: 12.5px;
        }
        .info-col {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .info-row {
            display: flex;
        }
        .info-label {
            width: 140px;
            color: #64748b;
            font-weight: 500;
            flex-shrink: 0;
        }
        .info-val {
            font-weight: 600;
            color: var(--ink);
        }

        /* Kartu Ringkasan Metrik */
        .section-heading {
            font-size: 14px;
            font-weight: 700;
            color: var(--ink);
            margin: 24px 0 12px 0;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-heading::before {
            content: "";
            width: 4px;
            height: 16px;
            background: #10b981;
            border-radius: 2px;
            display: inline-block;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            margin-bottom: 24px;
        }
        .stat-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 10px;
            text-align: center;
        }
        .stat-box .sb-lbl {
            display: block;
            font-size: 10.5px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .stat-box .sb-val {
            font-size: 16px;
            font-weight: 700;
            color: var(--ink);
        }

        /* Tabel Data Telemetri */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            margin-top: 10px;
        }
        .report-table th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            text-transform: uppercase;
            font-size: 10.5px;
        }
        .report-table td {
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }
        .report-table tr:nth-child(even) {
            background: #f8fafc;
        }
        .badge-status-print {
            font-size: 9.5px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .bsp-baik { background: #dcfce7; color: #166534; }
        .bsp-cukup { background: #fef9c3; color: #854d0e; }
        .bsp-waspada { background: #fee2e2; color: #991b1b; }

        /* Area Tanda Tangan */
        .signature-area {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px dashed #cbd5e1;
        }
        .sig-box {
            text-align: center;
        }
        .sig-box p {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 60px;
        }
        .sig-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--ink);
            text-decoration: underline;
        }
        .sig-role {
            font-size: 11.5px;
            color: #64748b;
        }

        /* ── ATURAN CETAK DOKUMEN PDF (CTRL + P) ── */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .pdf-top-actions {
                display: none !important;
            }
            .pdf-document {
                margin: 0 auto !important;
                padding: 24px 30px !important;
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
            }
            @page {
                size: A4 portrait;
                margin: 15mm 12mm;
            }
            .stats-grid {
                grid-template-columns: repeat(6, 1fr) !important;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    {{-- BAR AKSI ATAS (TIDAK TERCETAK DI PDF) --}}
    <nav class="pdf-top-actions">
        <div class="pta-left">
            <a href="{{ route('riwayat-sensor') }}" class="pta-back" title="Kembali ke Riwayat Sensor">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <span class="pta-title">Pratinjau Laporan PDF &mdash; {{ $device['nama'] }}</span>
        </div>
        <div class="pta-right">
            <span class="pta-hint"><i class="bi bi-info-circle"></i> Pilih "Save as PDF" di menu cetak browser</span>
            <button type="button" class="btn-print-now" onclick="window.print()">
                <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF
            </button>
        </div>
    </nav>

    {{-- KERTAS DOKUMEN RESMI --}}
    <main class="pdf-document">

        {{-- 1. KOP SURAT LAPORAN --}}
        <header class="doc-header">
            <div class="doc-brand">
                <div class="doc-logo">A</div>
                <div>
                    <div class="doc-company">AquaCrab IoT Systems</div>
                    <div class="doc-sub">Sistem Cerdas Pemantauan Kualitas Air Wadah Kepiting</div>
                </div>
            </div>
            <div class="doc-meta-right">
                <div class="doc-code">DOK: AQ-{{ $device['id'] }}-{{ date('Ymd') }}</div>
                <div class="doc-date">Dicetak: {{ $printedAt }}</div>
            </div>
        </header>

        {{-- 2. JUDUL LAPORAN --}}
        <section class="doc-title-box">
            <h1>Laporan Riwayat Telemetri Kualitas Air</h1>
            <p>Periode Pemantauan: <strong>{{ $periodeText }}</strong></p>
        </section>

        {{-- 3. PROFIL PERANGKAT WADAH BUDIDAYA --}}
        <section class="info-grid">
            <div class="info-col">
                <div class="info-row">
                    <span class="info-label">Nama Wadah:</span>
                    <span class="info-val">{{ $device['nama'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">ID Perangkat Node:</span>
                    <span class="info-val"><code>{{ $device['id'] }}</code></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Sektor Lokasi:</span>
                    <span class="info-val">{{ $device['lokasi'] }}</span>
                </div>
            </div>
            <div class="info-col">
                <div class="info-row">
                    <span class="info-label">Petambak Binaan:</span>
                    <span class="info-val">{{ $device['petambak'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Populasi Kepiting:</span>
                    <span class="info-val">{{ $device['populasi'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Data Tercatat:</span>
                    <span class="info-val">{{ $stats['total_entri'] }} entri pengukuran</span>
                </div>
            </div>
        </section>

        {{-- 4. TABEL RINCIAN PENGUKURAN TELEMETRI --}}
        <h2 class="section-heading">Log Pengukuran Telemetri Sensor</h2>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>pH</th>
                    <th>Salinitas</th>
                    <th>DO</th>
                    <th>Suhu</th>
                    <th>NTU</th>
                    <th>Level</th>
                    <th>Skor</th>
                    <th>Status</th>
                    <th>Aksi Aktuator</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($readings as $r)
                    <tr>
                        <td>{{ $r['tanggal'] }}</td>
                        <td><strong>{{ $r['waktu'] }}</strong></td>
                        <td>{{ $r['ph'] }}</td>
                        <td>{{ $r['sal'] }} <small>ppt</small></td>
                        <td>{{ $r['do'] }} <small>mg/L</small></td>
                        <td>{{ $r['suhu'] }} <small>°C</small></td>
                        <td>{{ $r['ntu'] }}</td>
                        <td>{{ $r['level'] }}%</td>
                        <td><strong>{{ $r['skor'] }}</strong></td>
                        <td>
                            <span class="badge-status-print bsp-{{ $r['status'] }}">
                                {{ strtoupper($r['status']) }}
                            </span>
                        </td>
                        <td>{{ $r['aktuator'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- 5. LEMBAR VALIDASI & TANDA TANGAN RESMI --}}
        <footer class="signature-area">
            <div class="sig-box">
                <p>Mengetahui / Pelaksana Budidaya,<br><strong>Petambak Binaan</strong></p>
                <div class="sig-name">{{ $device['petambak'] }}</div>
                <div class="sig-role">Mitra Tambak AquaCrab</div>
            </div>
            <div class="sig-box">
                <p>Diverifikasi oleh,<br><strong>Teknisi &amp; Operator Lapangan</strong></p>
                <div class="sig-name">Budi Santoso, S.Kel.</div>
                <div class="sig-role">Teknisi Pengawas Mutu Air IoT</div>
            </div>
        </footer>

    </main>

</body>
</html>

