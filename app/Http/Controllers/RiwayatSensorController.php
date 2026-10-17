<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiwayatSensorController extends Controller
{
    /**
     * Menampilkan halaman Riwayat Sensor beserta 48 data dummy parameter air.
     */
    public function index()
    {
        // ===== Menu sidebar =====
        $menus = [
            ['label' => 'Dashboard',          'icon' => 'bi-house',            'route' => 'dashboard'],
            ['label' => 'Riwayat Sensor',     'icon' => 'bi-clock-history',    'route' => 'riwayat-sensor'],
            ['label' => 'Inferensi Fuzzy',    'icon' => 'bi-diagram-3',        'route' => 'inferensi-fuzzy'],
            ['label' => 'Kontrol Aktuator',   'icon' => 'bi-sliders',          'route' => 'kontrol-aktuator'],
            ['label' => 'Notifikasi & Alarm', 'icon' => 'bi-alarm',            'route' => 'notifikasi-alarm'],
            ['label' => 'Kalibrasi Sensor',   'icon' => 'bi-file-earmark-text','route' => 'kalibrasi-sensor'],
            ['label' => 'Pengaturan',         'icon' => 'bi-gear',             'route' => 'pengaturan'],
            ['label' => 'Logout',             'icon' => 'bi-box-arrow-right',  'route' => 'logout'],
        ];

        // 48 entri riwayat parameter air per 15 menit (06:15 s.d. 18:00) pada 09/11/2026
        $readings = $this->getSensorHistoryData();

        $tanggal = '09/11/2026';
        $tanggalIso = '2026-11-09';
        $totalEntri = count($readings);

        return view('riwayat-sensor', compact('menus', 'readings', 'tanggal', 'tanggalIso', 'totalEntri'));
    }

    /**
     * Mengunduh data riwayat sensor dalam format CSV.
     */
    public function exportCsv()
    {
        $readings = $this->getSensorHistoryData();
        $filename = 'riwayat_sensor_09-11-2026.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($readings) {
            $handle = fopen('php://output', 'w');
            // Menambahkan UTF-8 BOM agar Excel dapat membaca karakter dengan benar
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header kolom CSV
            fputcsv($handle, ['Tanggal', 'Waktu', 'pH', 'Salinitas (ppt)', 'DO (mg/L)', 'Suhu (°C)', 'NTU', 'Level (%)', 'Skor', 'Status', 'Aksi Aktuator'], ';');

            foreach ($readings as $row) {
                fputcsv($handle, [
                    $row['tanggal'],
                    $row['waktu'],
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
     * Data dummy 48 entri parameter air (100% presisi sesuai desain Figma).
     */
    private function getSensorHistoryData(): array
    {
        $raw = [
            // ===== Halaman 1 (Baris 1 - 12: 06:15 s.d 09:00) =====
            ['waktu' => '06:15', 'ph' => '8,12', 'sal' => '24,8', 'do' => '5,82', 'suhu' => '29,1', 'ntu' => 12, 'level' => 84, 'skor' => '92,4', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '06:30', 'ph' => '8,18', 'sal' => '25,1', 'do' => '5,76', 'suhu' => '29,4', 'ntu' => 14, 'level' => 83, 'skor' => '91,8', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '06:45', 'ph' => '8,24', 'sal' => '25,4', 'do' => '5,69', 'suhu' => '29,7', 'ntu' => 16, 'level' => 82, 'skor' => '91,2', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '07:00', 'ph' => '8,30', 'sal' => '25,8', 'do' => '5,62', 'suhu' => '30,1', 'ntu' => 18, 'level' => 81, 'skor' => '90,6', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '07:15', 'ph' => '8,36', 'sal' => '26,1', 'do' => '5,55', 'suhu' => '30,5', 'ntu' => 20, 'level' => 80, 'skor' => '89,9', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '07:30', 'ph' => '8,42', 'sal' => '26,4', 'do' => '5,48', 'suhu' => '30,9', 'ntu' => 22, 'level' => 79, 'skor' => '89,1', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '07:45', 'ph' => '8,48', 'sal' => '26,7', 'do' => '5,41', 'suhu' => '31,3', 'ntu' => 24, 'level' => 78, 'skor' => '88,4', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '08:00', 'ph' => '8,54', 'sal' => '27,0', 'do' => '5,34', 'suhu' => '31,7', 'ntu' => 26, 'level' => 77, 'skor' => '87,7', 'status' => 'cukup',   'aktuator' => 'Tidak ada'],
            ['waktu' => '08:15', 'ph' => '8,60', 'sal' => '27,3', 'do' => '5,28', 'suhu' => '32,1', 'ntu' => 28, 'level' => 76, 'skor' => '87,0', 'status' => 'cukup',   'aktuator' => 'Tidak ada'],
            ['waktu' => '08:30', 'ph' => '8,66', 'sal' => '27,6', 'do' => '5,22', 'suhu' => '32,5', 'ntu' => 30, 'level' => 75, 'skor' => '86,3', 'status' => 'waspada', 'aktuator' => 'Tidak ada'],
            ['waktu' => '08:45', 'ph' => '8,72', 'sal' => '27,9', 'do' => '5,16', 'suhu' => '32,9', 'ntu' => 32, 'level' => 74, 'skor' => '85,6', 'status' => 'waspada', 'aktuator' => 'Tidak ada'],
            ['waktu' => '09:00', 'ph' => '8,78', 'sal' => '28,2', 'do' => '5,10', 'suhu' => '33,3', 'ntu' => 34, 'level' => 73, 'skor' => '84,9', 'status' => 'waspada', 'aktuator' => 'Tidak ada'],

            // ===== Halaman 2 (Baris 13 - 24: 09:15 s.d 12:00) =====
            ['waktu' => '09:15', 'ph' => '8,82', 'sal' => '28,5', 'do' => '5,04', 'suhu' => '33,6', 'ntu' => 35, 'level' => 72, 'skor' => '84,2', 'status' => 'waspada', 'aktuator' => 'Tidak ada'],
            ['waktu' => '09:30', 'ph' => '8,85', 'sal' => '28,8', 'do' => '4,98', 'suhu' => '33,8', 'ntu' => 36, 'level' => 71, 'skor' => '83,5', 'status' => 'waspada', 'aktuator' => 'Aerator ON'],
            ['waktu' => '09:45', 'ph' => '8,88', 'sal' => '29,0', 'do' => '4,92', 'suhu' => '34,0', 'ntu' => 38, 'level' => 70, 'skor' => '82,8', 'status' => 'waspada', 'aktuator' => 'Aerator ON'],
            ['waktu' => '10:00', 'ph' => '8,90', 'sal' => '29,2', 'do' => '4,86', 'suhu' => '34,1', 'ntu' => 40, 'level' => 70, 'skor' => '82,1', 'status' => 'waspada', 'aktuator' => 'Aerator ON'],
            ['waktu' => '10:15', 'ph' => '8,86', 'sal' => '29,1', 'do' => '4,95', 'suhu' => '33,9', 'ntu' => 39, 'level' => 71, 'skor' => '83,0', 'status' => 'waspada', 'aktuator' => 'Aerator ON'],
            ['waktu' => '10:30', 'ph' => '8,80', 'sal' => '29,0', 'do' => '5,05', 'suhu' => '33,5', 'ntu' => 37, 'level' => 72, 'skor' => '84,1', 'status' => 'waspada', 'aktuator' => 'Aerator ON'],
            ['waktu' => '10:45', 'ph' => '8,75', 'sal' => '28,7', 'do' => '5,14', 'suhu' => '33,1', 'ntu' => 35, 'level' => 73, 'skor' => '85,2', 'status' => 'waspada', 'aktuator' => 'Tidak ada'],
            ['waktu' => '11:00', 'ph' => '8,68', 'sal' => '28,4', 'do' => '5,22', 'suhu' => '32,7', 'ntu' => 33, 'level' => 74, 'skor' => '86,2', 'status' => 'waspada', 'aktuator' => 'Tidak ada'],
            ['waktu' => '11:15', 'ph' => '8,62', 'sal' => '28,0', 'do' => '5,30', 'suhu' => '32,3', 'ntu' => 31, 'level' => 75, 'skor' => '87,1', 'status' => 'cukup',   'aktuator' => 'Tidak ada'],
            ['waktu' => '11:30', 'ph' => '8,55', 'sal' => '27,7', 'do' => '5,38', 'suhu' => '31,9', 'ntu' => 29, 'level' => 76, 'skor' => '88,0', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '11:45', 'ph' => '8,50', 'sal' => '27,4', 'do' => '5,45', 'suhu' => '31,5', 'ntu' => 27, 'level' => 77, 'skor' => '88,8', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '12:00', 'ph' => '8,46', 'sal' => '27,1', 'do' => '5,50', 'suhu' => '31,2', 'ntu' => 25, 'level' => 78, 'skor' => '89,5', 'status' => 'baik',    'aktuator' => 'Tidak ada'],

            // ===== Halaman 3 (Baris 25 - 36: 12:15 s.d 15:00) =====
            ['waktu' => '12:15', 'ph' => '8,42', 'sal' => '26,8', 'do' => '5,55', 'suhu' => '30,9', 'ntu' => 23, 'level' => 79, 'skor' => '90,1', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '12:30', 'ph' => '8,38', 'sal' => '26,5', 'do' => '5,60', 'suhu' => '30,6', 'ntu' => 21, 'level' => 80, 'skor' => '90,7', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '12:45', 'ph' => '8,34', 'sal' => '26,2', 'do' => '5,65', 'suhu' => '30,3', 'ntu' => 19, 'level' => 81, 'skor' => '91,2', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '13:00', 'ph' => '8,30', 'sal' => '25,9', 'do' => '5,70', 'suhu' => '30,0', 'ntu' => 18, 'level' => 82, 'skor' => '91,7', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '13:15', 'ph' => '8,26', 'sal' => '25,6', 'do' => '5,74', 'suhu' => '29,8', 'ntu' => 17, 'level' => 82, 'skor' => '92,1', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '13:30', 'ph' => '8,22', 'sal' => '25,3', 'do' => '5,78', 'suhu' => '29,6', 'ntu' => 16, 'level' => 83, 'skor' => '92,5', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '13:45', 'ph' => '8,20', 'sal' => '25,1', 'do' => '5,80', 'suhu' => '29,4', 'ntu' => 15, 'level' => 83, 'skor' => '92,8', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '14:00', 'ph' => '8,18', 'sal' => '24,9', 'do' => '5,82', 'suhu' => '29,2', 'ntu' => 14, 'level' => 84, 'skor' => '93,0', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '14:15', 'ph' => '8,21', 'sal' => '25,1', 'do' => '5,79', 'suhu' => '29,5', 'ntu' => 15, 'level' => 83, 'skor' => '92,6', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '14:30', 'ph' => '8,25', 'sal' => '25,3', 'do' => '5,75', 'suhu' => '29,8', 'ntu' => 16, 'level' => 83, 'skor' => '92,1', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '14:45', 'ph' => '8,30', 'sal' => '25,6', 'do' => '5,70', 'suhu' => '30,1', 'ntu' => 18, 'level' => 82, 'skor' => '91,5', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '15:00', 'ph' => '8,35', 'sal' => '25,9', 'do' => '5,65', 'suhu' => '30,4', 'ntu' => 20, 'level' => 81, 'skor' => '90,9', 'status' => 'baik',    'aktuator' => 'Tidak ada'],

            // ===== Halaman 4 (Baris 37 - 48: 15:15 s.d 18:00) =====
            ['waktu' => '15:15', 'ph' => '8,40', 'sal' => '26,2', 'do' => '5,60', 'suhu' => '30,7', 'ntu' => 21, 'level' => 80, 'skor' => '90,2', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '15:30', 'ph' => '8,45', 'sal' => '26,5', 'do' => '5,54', 'suhu' => '31,0', 'ntu' => 23, 'level' => 79, 'skor' => '89,5', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '15:45', 'ph' => '8,50', 'sal' => '26,8', 'do' => '5,48', 'suhu' => '31,4', 'ntu' => 25, 'level' => 78, 'skor' => '88,7', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '16:00', 'ph' => '8,55', 'sal' => '27,1', 'do' => '5,42', 'suhu' => '31,8', 'ntu' => 27, 'level' => 77, 'skor' => '87,9', 'status' => 'cukup',   'aktuator' => 'Tidak ada'],
            ['waktu' => '16:15', 'ph' => '8,60', 'sal' => '27,4', 'do' => '5,36', 'suhu' => '32,1', 'ntu' => 29, 'level' => 76, 'skor' => '87,1', 'status' => 'cukup',   'aktuator' => 'Tidak ada'],
            ['waktu' => '16:30', 'ph' => '8,64', 'sal' => '27,7', 'do' => '5,30', 'suhu' => '32,4', 'ntu' => 31, 'level' => 75, 'skor' => '86,4', 'status' => 'waspada', 'aktuator' => 'Tidak ada'],
            ['waktu' => '16:45', 'ph' => '8,68', 'sal' => '28,0', 'do' => '5,24', 'suhu' => '32,7', 'ntu' => 33, 'level' => 74, 'skor' => '85,8', 'status' => 'waspada', 'aktuator' => 'Tidak ada'],
            ['waktu' => '17:00', 'ph' => '8,72', 'sal' => '28,2', 'do' => '5,18', 'suhu' => '33,0', 'ntu' => 35, 'level' => 73, 'skor' => '85,2', 'status' => 'waspada', 'aktuator' => 'Tidak ada'],
            ['waktu' => '17:15', 'ph' => '8,67', 'sal' => '27,9', 'do' => '5,25', 'suhu' => '32,5', 'ntu' => 32, 'level' => 74, 'skor' => '86,1', 'status' => 'waspada', 'aktuator' => 'Tidak ada'],
            ['waktu' => '17:30', 'ph' => '8,61', 'sal' => '27,5', 'do' => '5,32', 'suhu' => '32,0', 'ntu' => 29, 'level' => 76, 'skor' => '87,0', 'status' => 'cukup',   'aktuator' => 'Tidak ada'],
            ['waktu' => '17:45', 'ph' => '8,55', 'sal' => '27,2', 'do' => '5,40', 'suhu' => '31,5', 'ntu' => 26, 'level' => 77, 'skor' => '88,1', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
            ['waktu' => '18:00', 'ph' => '8,48', 'sal' => '26,8', 'do' => '5,48', 'suhu' => '31,0', 'ntu' => 24, 'level' => 78, 'skor' => '89,0', 'status' => 'baik',    'aktuator' => 'Tidak ada'],
        ];

        return array_map(function ($item) {
            $item['tanggal'] = '09/11/2026';
            $item['tanggal_format'] = '09 Nov 2026';
            return $item;
        }, $raw);
    }
}
