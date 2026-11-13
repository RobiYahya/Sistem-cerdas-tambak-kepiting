<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class RiwayatSensorController extends Controller
{
    /**
     * Menampilkan halaman Riwayat dan Laporan Kualitas Air.
     */
    public function index(Request $request)
    {
        // Proteksi: Jika belum login, harus ke halaman login dulu!
        if (! session('is_logged_in', false)) {
            return redirect()->route('login');
        }

        $userRole  = session('user_role', 'nelayan');
        $isTeknisi = ($userRole === 'teknisi');

        // Daftar Wadah Klaster Tambak Kepiting (RBAC aware)
        $allDevices = $this->getAllDevices();

        // Filter berdasarkan peran
        if ($userRole === 'nelayan') {
            $devices = array_values(array_filter($allDevices, fn($d) => in_array($d['id'], ['AQ-BOX-A1', 'AQ-BOX-B2'])));
        } else {
            $devices = $allDevices;
        }

        // Tentukan wadah terpilih
        $selectedDeviceId = $request->query('device', session('selected_device_id', 'AQ-BOX-A1'));
        $selectedDevice = collect($devices)->firstWhere('id', $selectedDeviceId) ?? $devices[0];

        session([
            'selected_device_id' => $selectedDevice['id'],
            'has_device'         => true,
        ]);

        // Menu sidebar (digunakan untuk bottom nav mobile)
        $menus = [
            ['label' => 'Perangkat Saya',   'icon' => 'bi-hdd-network',      'url' => route('dashboard'),                                              'active' => false],
            ['label' => 'Dashboard',        'icon' => 'bi-grid-fill',        'url' => route('dashboard', ['device' => $selectedDevice['id'] ?? '']),  'active' => false],
            ['label' => 'Analisis Kualitas Air', 'icon' => 'bi-water',      'url' => route('inferensi-fuzzy'),                                        'active' => false],
            ['label' => 'Riwayat & Laporan','icon' => 'bi-file-earmark-text','url' => route('riwayat-sensor'),                                        'active' => true],
            ['label' => 'Notifikasi Wadah', 'icon' => 'bi-bell',            'url' => route('notifikasi-wadah'),                                       'active' => false],
        ];
        // ===== Menu sidebar =====
        $menus = $this->getSidebarMenus();

        if ($isTeknisi) {
            $menus[] = ['label' => 'Kontrol Aktuator', 'icon' => 'bi-sliders', 'url' => route('kontrol-aktuator'), 'active' => false];
        }

        $menus[] = ['label' => 'Logout', 'icon' => 'bi-box-arrow-right', 'url' => route('logout'), 'active' => false];

        // Rentang tanggal dari query atau default hari ini
        $tanggalIso = '2026-11-11';
        $startDate  = $request->query('start_date', $tanggalIso);
        $endDate    = $request->query('end_date', $tanggalIso);

        // Ambil data pembacaan sensor sesuai rentang tanggal
        $readings   = $this->getReadingsForRange($startDate, $endDate);
        $totalEntri = count($readings);
        $tanggal    = Carbon::parse($startDate)->format('d/m/Y');
        if ($startDate !== $endDate) {
            $tanggal = Carbon::parse($startDate)->format('d/m/Y') . ' – ' . Carbon::parse($endDate)->format('d/m/Y');
        }

        // Data Grafik Dinamika Parameter Air Multi-Line
        $chartData = [
            'labels' => [
                'Mulai Periode', '02:00', '04:00', '06:00', '08:00',
                '10:00', '12:00', 'Tengah Periode', '16:00', '18:00',
                '20:00', '22:00', 'Sekarang'
            ],
            'suhu'      => [29.2, 29.8, 30.4, 30.8, 31.2, 29.7, 29.1, 30.3, 30.8, 30.5, 29.4, 31.8, 32.4],
            'salinitas' => [26.1, 25.8, 25.9, 26.4, 26.8, 26.3, 26.2, 26.5, 26.6, 26.4, 26.2, 26.3, 26.4],
            'do'        => [5.62, 5.68, 5.74, 5.70, 5.52, 5.48, 5.42, 5.58, 5.64, 5.55, 5.48, 5.62, 5.71],
            'ph'        => [8.12, 8.18, 8.24, 8.30, 8.14, 8.26, 8.32, 8.20, 8.28, 8.36, 8.22, 8.42, 8.51],
        ];

        return view('riwayat-sensor', compact(
            'userRole', 'isTeknisi', 'devices', 'selectedDevice',
            'menus', 'readings', 'tanggal', 'tanggalIso', 'startDate', 'endDate',
            'totalEntri', 'chartData'
        ));
    }

    /**
     * Mengunduh data riwayat sensor dalam format CSV sesuai rentang tanggal.
     */
    public function exportCsv(Request $request)
    {
        $startDate = $request->query('start_date', '2026-11-11');
        $endDate   = $request->query('end_date', $startDate);
        $deviceId  = $request->query('device', session('selected_device_id', 'AQ-BOX-A1'));

        $device   = collect($this->getAllDevices())->firstWhere('id', $deviceId) ?? $this->getAllDevices()[0];
        $readings = $this->getReadingsForRange($startDate, $endDate);

        $filename = "laporan_kualitas_air_{$deviceId}_{$startDate}_sd_{$endDate}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($readings, $device) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM untuk Excel

            // Metadata Laporan di baris awal
            fputcsv($handle, ['LAPORAN DATA TELEMETRI SENSOR KUALITAS AIR - AQUACRAB IOT'], ';');
            fputcsv($handle, ['Wadah Budidaya', $device['nama'] . " ({$device['id']})"], ';');
            fputcsv($handle, ['Lokasi', $device['lokasi']], ';');
            fputcsv($handle, ['Petambak Binaan', $device['petambak']], ';');
            fputcsv($handle, ['Periode Ekspor', "{$readings[0]['tanggal']} s.d. " . end($readings)['tanggal']], ';');
            fputcsv($handle, ['Total Data', count($readings) . ' baris tercatat'], ';');
            fputcsv($handle, [], ';'); // Baris kosong pemisah

            // Header kolom data
            fputcsv($handle, [
                'Tanggal', 'Waktu', 'ID Wadah', 'pH Air', 'Salinitas (ppt)',
                'DO (mg/L)', 'Suhu Air (°C)', 'Kekeruhan (NTU)', 'Level Air (%)',
                'Skor Kualitas', 'Kategori Status', 'Aksi Aktuator Sistem'
            ], ';');

            foreach ($readings as $row) {
                fputcsv($handle, [
                    $row['tanggal'],
                    $row['waktu'],
                    $device['id'],
                    $row['ph'],
                    $row['sal'],
                    $row['do'],
                    $row['suhu'],
                    $row['ntu'],
                    $row['level'],
                    $row['skor'],
                    strtoupper($row['status']),
                    $row['aktuator'],
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Menampilkan dokumen cetak / download PDF resmi sesuai rentang tanggal.
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->query('start_date', '2026-11-11');
        $endDate   = $request->query('end_date', $startDate);
        $deviceId  = $request->query('device', session('selected_device_id', 'AQ-BOX-A1'));

        $device   = collect($this->getAllDevices())->firstWhere('id', $deviceId) ?? $this->getAllDevices()[0];
        $readings = $this->getReadingsForRange($startDate, $endDate);

        // Hitung statistik ringkasan untuk laporan PDF
        $total = count($readings);
        $avgPh   = $total > 0 ? round(collect($readings)->avg(fn($r) => floatval(str_replace(',', '.', $r['ph']))), 2) : 8.35;
        $avgDo   = $total > 0 ? round(collect($readings)->avg(fn($r) => floatval(str_replace(',', '.', $r['do']))), 2) : 5.60;
        $avgSal  = $total > 0 ? round(collect($readings)->avg(fn($r) => floatval(str_replace(',', '.', $r['sal']))), 1) : 26.2;
        $avgSuhu = $total > 0 ? round(collect($readings)->avg(fn($r) => floatval(str_replace(',', '.', $r['suhu']))), 1) : 30.8;
        $avgNtu  = $total > 0 ? round(collect($readings)->avg(fn($r) => floatval(str_replace(',', '.', $r['ntu']))), 0) : 24;
        $avgSkor = $total > 0 ? round(collect($readings)->avg(fn($r) => floatval(str_replace(',', '.', $r['skor']))), 1) : 75.3;

        $stats = [
            'total_entri' => $total,
            'avg_ph'      => number_format($avgPh, 2, ',', '.'),
            'avg_do'      => number_format($avgDo, 2, ',', '.') . ' mg/L',
            'avg_sal'     => number_format($avgSal, 1, ',', '.') . ' ppt',
            'avg_suhu'    => number_format($avgSuhu, 1, ',', '.') . ' °C',
            'avg_ntu'     => $avgNtu . ' NTU',
            'avg_skor'    => number_format($avgSkor, 1, ',', '.'),
            'status'      => $avgSkor >= 80 ? 'SANGAT BAIK' : ($avgSkor >= 70 ? 'BAIK' : 'CUKUP'),
        ];

        $printedAt = Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB';
        $periodeText = Carbon::parse($startDate)->translatedFormat('d M Y') . ' s.d. ' . Carbon::parse($endDate)->translatedFormat('d M Y');
        if ($startDate === $endDate) {
            $periodeText = Carbon::parse($startDate)->translatedFormat('d F Y');
        }

        return view('laporan-pdf', compact(
            'device', 'readings', 'startDate', 'endDate', 'stats', 'printedAt', 'periodeText'
        ));
    }

    /**
     * Mengambil entri data sensor untuk rentang tanggal tertentu.
     */
    private function getReadingsForRange(string $startDate, string $endDate): array
    {
        $baseRaw = $this->getBaseDailyData();

        $start = Carbon::parse($startDate);
        $end   = Carbon::parse($endDate);

        if ($start->gt($end)) {
            $tmp = $start;
            $start = $end;
            $end = $tmp;
        }

        $diffDays = $start->diffInDays($end);

        // Jika satu hari (single date): return 48 data pada hari tersebut
        if ($diffDays === 0) {
            $dateStr = $start->format('d/m/Y');
            $dateFmt = $start->translatedFormat('d M Y');
            return array_map(function ($item) use ($dateStr, $dateFmt) {
                $item['tanggal'] = $dateStr;
                $item['tanggal_format'] = $dateFmt;
                return $item;
            }, $baseRaw);
        }

        // Jika rentang beberapa hari (multi-date):
        // Bangun data periodik harian representatif (maksimal 60 entri agar optimal di tabel/PDF)
        $readings = [];
        $step = max(1, intval(ceil($diffDays / 7))); // interval per hari jika rentang panjang

        $curr = $start->copy();
        while ($curr->lte($end)) {
            $dateStr = $curr->format('d/m/Y');
            $dateFmt = $curr->translatedFormat('d M Y');

            // Ambil sampel jam (06:00, 09:00, 12:00, 15:00, 18:00, 21:00) untuk hari bersangkutan
            $samples = [
                ['waktu' => '06:00', 'ph' => '8,15', 'sal' => '25,2', 'do' => '5,80', 'suhu' => '29,0', 'ntu' => 14, 'level' => 84, 'skor' => '92,1', 'status' => 'baik',    'aktuator' => 'Standby'],
                ['waktu' => '09:00', 'ph' => '8,28', 'sal' => '25,8', 'do' => '5,65', 'suhu' => '30,2', 'ntu' => 18, 'level' => 81, 'skor' => '90,5', 'status' => 'baik',    'aktuator' => 'Standby'],
                ['waktu' => '12:00', 'ph' => '8,42', 'sal' => '26,5', 'do' => '5,50', 'suhu' => '31,4', 'ntu' => 24, 'level' => 78, 'skor' => '88,2', 'status' => 'baik',    'aktuator' => 'Standby'],
                ['waktu' => '15:00', 'ph' => '8,50', 'sal' => '27,0', 'do' => '5,40', 'suhu' => '32,1', 'ntu' => 28, 'level' => 76, 'skor' => '86,8', 'status' => 'cukup',   'aktuator' => 'Aerator Venturi Aktif'],
                ['waktu' => '18:00', 'ph' => '8,51', 'sal' => '26,4', 'do' => '5,71', 'suhu' => '32,4', 'ntu' => 34, 'level' => 78, 'skor' => '75,3', 'status' => 'baik',    'aktuator' => 'Aerator + Mist Cooler'],
                ['waktu' => '21:00', 'ph' => '8,35', 'sal' => '26,0', 'do' => '5,75', 'suhu' => '30,5', 'ntu' => 22, 'level' => 80, 'skor' => '89,4', 'status' => 'baik',    'aktuator' => 'Standby'],
            ];

            foreach ($samples as $s) {
                $s['tanggal'] = $dateStr;
                $s['tanggal_format'] = $dateFmt;
                $readings[] = $s;
            }

            $curr->addDays($step);
        }

        return $readings;
    }

    /**
     * Daftar seluruh perangkat tambak.
     */
    private function getAllDevices(): array
    {
        return [
            [
                'id'        => 'AQ-BOX-A1',
                'nama'      => 'Unit 01 — Apartemen Kepiting Soka A1',
                'tipe'      => 'Apartemen Box Vertikal',
                'lokasi'    => 'Sektor Utara Tambak A1',
                'petambak'  => 'Pak Robi (Petambak Binaan)',
                'populasi'  => '850 ekor',
                'rssi'      => '-68 dBm',
                'baterai'   => '94%',
                'status'    => 'online',
                'last_seen' => '10 detik lalu',
                'kualitas'  => 'BAIK',
                'indeks'    => '75,3',
            ],
            [
                'id'        => 'AQ-BOX-B2',
                'nama'      => 'Unit 02 — Kolam Pembesaran B2',
                'tipe'      => 'Apartemen Box Vertikal',
                'lokasi'    => 'Sektor Barat Tambak B',
                'petambak'  => 'Pak Robi (Petambak Binaan)',
                'populasi'  => '620 ekor',
                'rssi'      => '-72 dBm',
                'baterai'   => '88%',
                'status'    => 'online',
                'last_seen' => '30 detik lalu',
                'kualitas'  => 'SANGAT BAIK',
                'indeks'    => '82,0',
            ],
            [
                'id'        => 'AQ-BOX-C1',
                'nama'      => 'Unit 03 — Kolam Karantina & Aklimatisasi C1',
                'tipe'      => 'Kolam Terpal Mini',
                'lokasi'    => 'Bangsal Riset & Karantina',
                'petambak'  => 'Pak Budi Hartono',
                'populasi'  => '120 ekor',
                'rssi'      => '-74 dBm',
                'baterai'   => '71%',
                'status'    => 'online',
                'last_seen' => '1 menit lalu',
                'kualitas'  => 'CUKUP',
                'indeks'    => '68,5',
            ],
            [
                'id'        => 'AQ-BOX-D4',
                'nama'      => 'Unit 04 — Jerigen Larva D4',
                'tipe'      => 'Jerigen 20 Liter',
                'lokasi'    => 'Area Pembibitan D',
                'petambak'  => 'Pak Budi Hartono',
                'populasi'  => '240 ekor',
                'rssi'      => 'N/A',
                'baterai'   => '0%',
                'status'    => 'offline',
                'last_seen' => '14 menit lalu',
                'kualitas'  => 'OFFLINE',
                'indeks'    => '71,2',
            ],
        ];
    }

    /**
     * 48 entri parameter air harian standar (per 15 menit).
     */
    private function getBaseDailyData(): array
    {
        return [
            ['waktu' => '06:15', 'ph' => '8,12', 'sal' => '24,8', 'do' => '5,82', 'suhu' => '29,1', 'ntu' => 12, 'level' => 84, 'skor' => '92,4', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '06:30', 'ph' => '8,18', 'sal' => '25,1', 'do' => '5,76', 'suhu' => '29,4', 'ntu' => 14, 'level' => 83, 'skor' => '91,8', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '06:45', 'ph' => '8,24', 'sal' => '25,4', 'do' => '5,69', 'suhu' => '29,7', 'ntu' => 16, 'level' => 82, 'skor' => '91,2', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '07:00', 'ph' => '8,30', 'sal' => '25,8', 'do' => '5,62', 'suhu' => '30,1', 'ntu' => 18, 'level' => 81, 'skor' => '90,6', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '07:15', 'ph' => '8,25', 'sal' => '25,5', 'do' => '5,67', 'suhu' => '29,8', 'ntu' => 17, 'level' => 82, 'skor' => '91,1', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '07:30', 'ph' => '8,20', 'sal' => '25,2', 'do' => '5,72', 'suhu' => '29,5', 'ntu' => 15, 'level' => 83, 'skor' => '91,6', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '07:45', 'ph' => '8,15', 'sal' => '24,9', 'do' => '5,77', 'suhu' => '29,2', 'ntu' => 13, 'level' => 83, 'skor' => '92,2', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '08:00', 'ph' => '8,12', 'sal' => '24,7', 'do' => '5,80', 'suhu' => '29,0', 'ntu' => 12, 'level' => 84, 'skor' => '92,5', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '08:15', 'ph' => '8,16', 'sal' => '25,0', 'do' => '5,78', 'suhu' => '29,3', 'ntu' => 14, 'level' => 83, 'skor' => '92,0', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '08:30', 'ph' => '8,21', 'sal' => '25,3', 'do' => '5,74', 'suhu' => '29,6', 'ntu' => 16, 'level' => 82, 'skor' => '91,4', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '08:45', 'ph' => '8,27', 'sal' => '25,6', 'do' => '5,68', 'suhu' => '29,9', 'ntu' => 18, 'level' => 81, 'skor' => '90,8', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '09:00', 'ph' => '8,32', 'sal' => '26,0', 'do' => '5,63', 'suhu' => '30,2', 'ntu' => 20, 'level' => 80, 'skor' => '90,3', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '09:15', 'ph' => '8,36', 'sal' => '26,3', 'do' => '5,58', 'suhu' => '30,5', 'ntu' => 22, 'level' => 80, 'skor' => '89,7', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '09:30', 'ph' => '8,41', 'sal' => '26,6', 'do' => '5,52', 'suhu' => '30,8', 'ntu' => 24, 'level' => 79, 'skor' => '89,1', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '09:45', 'ph' => '8,46', 'sal' => '26,9', 'do' => '5,46', 'suhu' => '31,2', 'ntu' => 26, 'level' => 78, 'skor' => '88,3', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '10:00', 'ph' => '8,50', 'sal' => '27,2', 'do' => '5,40', 'suhu' => '31,5', 'ntu' => 28, 'level' => 77, 'skor' => '87,6', 'status' => 'cukup',   'aktuator' => 'Aerator Venturi Aktif'],
            ['waktu' => '10:15', 'ph' => '8,54', 'sal' => '27,5', 'do' => '5,35', 'suhu' => '31,8', 'ntu' => 30, 'level' => 76, 'skor' => '86,9', 'status' => 'cukup',   'aktuator' => 'Aerator Venturi Aktif'],
            ['waktu' => '10:30', 'ph' => '8,58', 'sal' => '27,8', 'do' => '5,30', 'suhu' => '32,1', 'ntu' => 32, 'level' => 75, 'skor' => '86,2', 'status' => 'waspada', 'aktuator' => 'Aerator + Mist Cooler'],
            ['waktu' => '10:45', 'ph' => '8,62', 'sal' => '28,0', 'do' => '5,25', 'suhu' => '32,4', 'ntu' => 34, 'level' => 75, 'skor' => '85,6', 'status' => 'waspada', 'aktuator' => 'Aerator + Mist Cooler'],
            ['waktu' => '11:00', 'ph' => '8,56', 'sal' => '27,7', 'do' => '5,32', 'suhu' => '32,0', 'ntu' => 31, 'level' => 76, 'skor' => '86,5', 'status' => 'waspada', 'aktuator' => 'Aerator Venturi Aktif'],
            ['waktu' => '11:15', 'ph' => '8,50', 'sal' => '27,3', 'do' => '5,40', 'suhu' => '31,6', 'ntu' => 28, 'level' => 77, 'skor' => '87,5', 'status' => 'cukup',   'aktuator' => 'Aerator Venturi Aktif'],
            ['waktu' => '11:30', 'ph' => '8,45', 'sal' => '27,0', 'do' => '5,48', 'suhu' => '31,2', 'ntu' => 26, 'level' => 78, 'skor' => '88,4', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '11:45', 'ph' => '8,48', 'sal' => '27,2', 'do' => '5,45', 'suhu' => '31,4', 'ntu' => 27, 'level' => 77, 'skor' => '88,0', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '12:00', 'ph' => '8,44', 'sal' => '26,9', 'do' => '5,52', 'suhu' => '31,1', 'ntu' => 25, 'level' => 78, 'skor' => '88,9', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '12:15', 'ph' => '8,41', 'sal' => '26,7', 'do' => '5,56', 'suhu' => '30,9', 'ntu' => 23, 'level' => 79, 'skor' => '90,1', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '12:30', 'ph' => '8,38', 'sal' => '26,5', 'do' => '5,60', 'suhu' => '30,6', 'ntu' => 21, 'level' => 80, 'skor' => '90,7', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '12:45', 'ph' => '8,34', 'sal' => '26,2', 'do' => '5,65', 'suhu' => '30,3', 'ntu' => 19, 'level' => 81, 'skor' => '91,2', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '13:00', 'ph' => '8,30', 'sal' => '25,9', 'do' => '5,70', 'suhu' => '30,0', 'ntu' => 18, 'level' => 82, 'skor' => '91,7', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '13:15', 'ph' => '8,26', 'sal' => '25,6', 'do' => '5,74', 'suhu' => '29,8', 'ntu' => 17, 'level' => 82, 'skor' => '92,1', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '13:30', 'ph' => '8,22', 'sal' => '25,3', 'do' => '5,78', 'suhu' => '29,6', 'ntu' => 16, 'level' => 83, 'skor' => '92,5', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '13:45', 'ph' => '8,20', 'sal' => '25,1', 'do' => '5,80', 'suhu' => '29,4', 'ntu' => 15, 'level' => 83, 'skor' => '92,8', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '14:00', 'ph' => '8,18', 'sal' => '24,9', 'do' => '5,82', 'suhu' => '29,2', 'ntu' => 14, 'level' => 84, 'skor' => '93,0', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '14:15', 'ph' => '8,21', 'sal' => '25,1', 'do' => '5,79', 'suhu' => '29,5', 'ntu' => 15, 'level' => 83, 'skor' => '92,6', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '14:30', 'ph' => '8,25', 'sal' => '25,3', 'do' => '5,75', 'suhu' => '29,8', 'ntu' => 16, 'level' => 83, 'skor' => '92,1', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '14:45', 'ph' => '8,30', 'sal' => '25,6', 'do' => '5,70', 'suhu' => '30,1', 'ntu' => 18, 'level' => 82, 'skor' => '91,5', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '15:00', 'ph' => '8,35', 'sal' => '25,9', 'do' => '5,65', 'suhu' => '30,4', 'ntu' => 20, 'level' => 81, 'skor' => '90,9', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '15:15', 'ph' => '8,40', 'sal' => '26,2', 'do' => '5,60', 'suhu' => '30,7', 'ntu' => 21, 'level' => 80, 'skor' => '90,2', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '15:30', 'ph' => '8,45', 'sal' => '26,5', 'do' => '5,54', 'suhu' => '31,0', 'ntu' => 23, 'level' => 79, 'skor' => '89,5', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '15:45', 'ph' => '8,50', 'sal' => '26,8', 'do' => '5,48', 'suhu' => '31,4', 'ntu' => 25, 'level' => 78, 'skor' => '88,7', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '16:00', 'ph' => '8,55', 'sal' => '27,1', 'do' => '5,42', 'suhu' => '31,8', 'ntu' => 27, 'level' => 77, 'skor' => '87,9', 'status' => 'cukup',   'aktuator' => 'Aerator Venturi Aktif'],
            ['waktu' => '16:15', 'ph' => '8,60', 'sal' => '27,4', 'do' => '5,36', 'suhu' => '32,1', 'ntu' => 29, 'level' => 76, 'skor' => '87,1', 'status' => 'cukup',   'aktuator' => 'Aerator Venturi Aktif'],
            ['waktu' => '16:30', 'ph' => '8,64', 'sal' => '27,7', 'do' => '5,30', 'suhu' => '32,4', 'ntu' => 31, 'level' => 75, 'skor' => '86,4', 'status' => 'waspada', 'aktuator' => 'Aerator + Mist Cooler'],
            ['waktu' => '16:45', 'ph' => '8,68', 'sal' => '28,0', 'do' => '5,24', 'suhu' => '32,7', 'ntu' => 33, 'level' => 74, 'skor' => '85,8', 'status' => 'waspada', 'aktuator' => 'Aerator + Mist Cooler'],
            ['waktu' => '17:00', 'ph' => '8,72', 'sal' => '28,2', 'do' => '5,18', 'suhu' => '33,0', 'ntu' => 35, 'level' => 73, 'skor' => '85,2', 'status' => 'waspada', 'aktuator' => 'Aerator + Mist Cooler'],
            ['waktu' => '17:15', 'ph' => '8,67', 'sal' => '27,9', 'do' => '5,25', 'suhu' => '32,5', 'ntu' => 32, 'level' => 74, 'skor' => '86,1', 'status' => 'waspada', 'aktuator' => 'Aerator + Mist Cooler'],
            ['waktu' => '17:30', 'ph' => '8,61', 'sal' => '27,5', 'do' => '5,32', 'suhu' => '32,0', 'ntu' => 29, 'level' => 76, 'skor' => '87,0', 'status' => 'cukup',   'aktuator' => 'Aerator Venturi Aktif'],
            ['waktu' => '17:45', 'ph' => '8,55', 'sal' => '27,2', 'do' => '5,40', 'suhu' => '31,5', 'ntu' => 26, 'level' => 77, 'skor' => '88,1', 'status' => 'baik',    'aktuator' => 'Standby'],
            ['waktu' => '18:00', 'ph' => '8,51', 'sal' => '26,4', 'do' => '5,71', 'suhu' => '32,4', 'ntu' => 34, 'level' => 78, 'skor' => '89,0', 'status' => 'baik',    'aktuator' => 'Standby'],
        ];
    }
}
