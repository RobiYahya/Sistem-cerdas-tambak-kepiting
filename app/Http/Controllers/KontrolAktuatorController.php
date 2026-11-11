<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KontrolAktuatorController extends Controller
{
    public function index()
    {
        // Proteksi: Jika belum login, harus ke halaman login dulu!
        if (! session('is_logged_in', false)) {
            return redirect()->route('login');
        }

        // Proteksi: Jika belum ada perangkat terdaftar, arahkan ke dashboard
        if (! session('has_device', false)) {
            return redirect()->route('dashboard')->with('pesan_warning', 'Pilih perangkat tambak terlebih dahulu untuk mengakses kontrol aktuator.');
        }

        $selectedId = session('selected_device_id');
        $dashboardUrl = $selectedId ? route('dashboard', ['device' => $selectedId]) : route('dashboard');

        // Proteksi Hak Akses (RBAC):
        // Nelayan hanya memiliki akses monitoring (read-only). Kontrol manual aktuator hanya boleh diakses oleh Teknisi / Admin.
        if (session('user_role', 'teknisi') === 'nelayan') {
            return redirect($dashboardUrl)->with('pesan_warning', 'Akses dibatasi: Kontrol manual aktuator hanya dapat dioperasikan oleh Teknisi Tambak.');
        }

        $menus = [
            ['label' => 'Perangkat Saya',   'icon' => 'bi-hdd-network',    'url' => route('dashboard'), 'active' => false],
            ['label' => 'Dashboard',        'icon' => 'bi-speedometer2',  'url' => $dashboardUrl,      'active' => false],
            ['label' => 'Riwayat Sensor',   'icon' => 'bi-clock-history', 'url' => route('riwayat-sensor'), 'active' => false],
            ['label' => 'Inferensi Fuzzy',  'icon' => 'bi-diagram-3',     'url' => route('inferensi-fuzzy'), 'active' => false],
            ['label' => 'Kontrol Aktuator', 'icon' => 'bi-sliders',       'url' => route('kontrol-aktuator'), 'active' => true],
            ['label' => 'Logout',           'icon' => 'bi-box-arrow-right', 'url' => route('logout'),   'active' => false],
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