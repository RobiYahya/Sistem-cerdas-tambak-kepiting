<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KontrolAktuatorController extends Controller
{
    public function index()
    {
        $menus = [
            ['label' => 'Dashboard',          'icon' => 'bi-house',             'route' => 'dashboard'],
            ['label' => 'Riwayat Sensor',     'icon' => 'bi-clock-history',     'route' => 'riwayat-sensor'],
            ['label' => 'Inferensi Fuzzy',    'icon' => 'bi-diagram-3',         'route' => 'inferensi-fuzzy'],
            ['label' => 'Kontrol Aktuator',   'icon' => 'bi-sliders',           'route' => 'kontrol-aktuator'],
            ['label' => 'Notifikasi & Alarm', 'icon' => 'bi-alarm',             'route' => 'notifikasi-alarm'],
            ['label' => 'Kalibrasi Sensor',   'icon' => 'bi-file-earmark-text', 'route' => 'kalibrasi-sensor'],
            ['label' => 'Pengaturan',         'icon' => 'bi-gear',              'route' => 'pengaturan'],
        ];

        $kualitas = [
            'indeks'  => 75.3,
            'status'  => 'BAIK',
            'kimia'   => 72,
            'osmotik' => 78,
            'fisik'   => 76,
        ];

        $parameter = [
            ['label' => 'Derajat Keasaman', 'nilai' => 8.48, 'satuan' => 'pH',   'status' => 'normal',  'label_status' => 'NORMAL',  'persen' => 73],
            ['label' => 'Salinitas',        'nilai' => 26.4, 'satuan' => 'ppt',  'status' => 'normal',  'label_status' => 'OPTIMAL', 'persen' => 66],
            ['label' => 'Suhu Air',         'nilai' => 32.4, 'satuan' => 'C',    'status' => 'waspada', 'label_status' => 'WASPADA', 'persen' => 81],
            ['label' => 'Oksigen Terlarut', 'nilai' => 5.40, 'satuan' => 'mg/L', 'status' => 'cukup',   'label_status' => 'CUKUP',   'persen' => 54],
            ['label' => 'Kekeruhan',        'nilai' => 34,   'satuan' => 'NTU',  'status' => 'cukup',   'label_status' => 'CUKUP',   'persen' => 43],
            ['label' => 'Level Air',        'nilai' => 78,   'satuan' => '%',    'status' => 'normal',  'label_status' => 'NORMAL',  'persen' => 78],
        ];

        $aktuator = [
            ['id' => 'solenoid-valve',  'nama' => 'Solenoid Valve',  'status' => 'Standby', 'kondisi' => 'Salinitas < 22 ppt',  'aktif' => false],
            ['id' => 'aerator',         'nama' => 'Aerator',         'status' => 'Standby', 'kondisi' => 'DO Rendah',           'aktif' => false],
            ['id' => 'dosing-pump',     'nama' => 'Dosing Pump',     'status' => 'Standby', 'kondisi' => 'Alternatif',          'aktif' => false],
            ['id' => 'pompa-sirkulasi', 'nama' => 'Pompa Sirkulasi', 'status' => 'Standby', 'kondisi' => 'Kekeruhan > 80 NTU', 'aktif' => false],
        ];

        $modeOtomatis = true;

        return view('kontrol-aktuator', compact(
            'menus', 'kualitas', 'parameter', 'aktuator', 'modeOtomatis'
        ));
    }
}