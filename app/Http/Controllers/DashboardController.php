<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
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

        // ===== Status kualitas air =====
        $kualitas = [
            'status' => 'BAIK',
            'indeks' => 75.3,
            'sub'    => [
                ['label' => 'Indeks Kimiawi', 'nilai' => 72],
                ['label' => 'Indeks Osmotik', 'nilai' => 78],
                ['label' => 'Indeks Fisik',   'nilai' => 76],
            ],
        ];

        // ===== Parameter air (kartu geser) =====
        // status: normal | cukup | waspada | kritis ; persen = panjang progress bar
        $parameters = [
            ['nama' => 'Derajat Keasaman', 'nilai' => '8,48', 'satuan' => 'pH',   'status' => 'normal',  'persen' => 70],
            ['nama' => 'Oksigen Terlarut', 'nilai' => '5,40', 'satuan' => 'mg/L', 'status' => 'cukup',   'persen' => 50],
            ['nama' => 'Suhu Air',         'nilai' => '32,4', 'satuan' => '°C',   'status' => 'waspada', 'persen' => 82],
            ['nama' => 'Level Air',        'nilai' => '78',   'satuan' => '%',    'status' => 'normal',  'persen' => 78],
            ['nama' => 'Salinitas',        'nilai' => '30,2', 'satuan' => 'ppt',  'status' => 'normal',  'persen' => 64],
            ['nama' => 'Kekeruhan',        'nilai' => '12,5', 'satuan' => 'NTU',  'status' => 'cukup',   'persen' => 45],
        ];

        // ===== Grafik tren (data dummy 25 titik: jam 00 - 24) =====
        $trends = [
            [
                'id' => 'ph', 'nama' => 'Derajat Keasaman', 'nilai' => '8,48 pH', 'status' => 'normal',
                'min' => 7, 'max' => 9, 'step' => 1,
                'data' => [7.0,7.4,7.9,8.3,8.5,8.6,8.6,8.3,8.1,8.1,8.0,7.9,8.1,8.3,8.4,8.5,8.6,8.7,8.8,8.6,8.4,8.2,8.1,8.0,8.0],
            ],
            [
                'id' => 'do', 'nama' => 'Oksigen Terlarut', 'nilai' => '5,40 mg/L', 'status' => 'cukup',
                'min' => 4, 'max' => 8, 'step' => 2,
                'data' => [5.2,5.5,5.8,6.0,6.1,6.1,6.0,6.0,6.0,6.1,6.3,6.6,7.2,6.7,6.6,6.5,6.3,6.0,5.7,5.4,5.0,4.7,4.4,4.2,4.0],
            ],
            [
                'id' => 'suhu', 'nama' => 'Suhu Air', 'nilai' => '32,4 °C', 'status' => 'waspada',
                'min' => 25, 'max' => 35, 'step' => 5,
                'data' => [29.5,29,28.3,27.4,26.5,26,25.7,25.5,25.5,25.4,25.7,26.5,27.6,29.3,30.8,32,33,33.2,33.3,33.3,33,32.8,32.2,31.4,31.5],
            ],
            [
                'id' => 'level', 'nama' => 'Level Air', 'nilai' => '78%', 'status' => 'normal',
                'min' => 50, 'max' => 100, 'step' => 25,
                'data' => [52,60,70,82,90,94,95,93,76,70,72,78,85,90,92,91,86,78,70,66,64,62,68,74,72],
            ],
            [
                'id' => 'salinitas', 'nama' => 'Salinitas', 'nilai' => '30,2 ppt', 'status' => 'normal',
                'min' => 25, 'max' => 35, 'step' => 5,
                'data' => [29,29.4,29.8,30,30.3,30.6,30.8,30.5,30.2,30,29.8,29.9,30.1,30.4,30.7,31,30.8,30.5,30.3,30.1,30,30.2,30.3,30.2,30.2],
            ],
            [
                'id' => 'turbid', 'nama' => 'Kekeruhan', 'nilai' => '12,5 NTU', 'status' => 'cukup',
                'min' => 0, 'max' => 20, 'step' => 10,
                'data' => [8,8.5,9,10,11,12,13,14,15,14,13,12.5,12,11,10.5,11,12,13,14,13.5,13,12.8,12.6,12.5,12.5],
            ],
        ];

        // ===== Notifikasi terbaru =====
        $alerts = array_fill(0, 3, [
            'tipe'   => 'ALERT KRITIS',
            'lokasi' => 'Kolam A',
            'pesan'  => 'ALERT System: pH Kolam A mencapai 8.9 (Kritis).',
        ]);

        return view('dashboard', compact('menus', 'kualitas', 'parameters', 'trends', 'alerts'));
    }
}