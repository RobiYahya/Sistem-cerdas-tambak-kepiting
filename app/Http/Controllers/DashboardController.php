<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Halaman Dashboard Utama: Multi-Device Management & Monitoring
     */
    public function index(Request $request)
    {
        // Proteksi: Jika belum login, harus ke halaman login dulu!
        if (! session('is_logged_in', false)) {
            return redirect()->route('login');
        }

        $userRole = session('user_role', 'teknisi');
        $allDevices = $this->getDevices();

        // Filter perangkat berdasarkan peran (RBAC):
        // - Nelayan (Farmer): Hanya melihat wadah jerigen/box kepiting binaan akunnya (Unit 01 & Unit 02)
        // - Teknisi (Admin): Melihat dan mengelola seluruh unit wadah dalam klaster tambak
        if ($userRole === 'nelayan') {
            $devices = array_values(array_filter($allDevices, function ($d) {
                return in_array($d['id'], ['AQ-BOX-A1', 'AQ-BOX-B2']);
            }));
        } else {
            $devices = $allDevices;
        }

        $totalDevices = count($devices);
        $onlineCount = collect($devices)->where('status', 'online')->count();
        $offlineCount = $totalDevices - $onlineCount;

        // Metrik Ringkasan Klaster Wadah Budidaya
        $optimalCount = collect($devices)->filter(function ($d) {
            $val = floatval(str_replace(',', '.', $d['indeks'] ?? 0));
            return $val >= 70;
        })->count();

        $warningCount = $totalDevices - $optimalCount;

        $avgQualityRaw = $totalDevices > 0 ? (collect($devices)->sum(function ($d) {
            return floatval(str_replace(',', '.', $d['indeks'] ?? 0));
        }) / $totalDevices) : 0;
        $avgQuality = number_format($avgQualityRaw, 1, ',', '.');

        // Cek apakah user sedang membuka detail monitoring perangkat tertentu (?device=CRAB-ESP32-XXX)
        $selectedDeviceId = $request->query('device');
        $selectedDevice = null;

        if ($selectedDeviceId && $totalDevices > 0) {
            $selectedDevice = collect($devices)->firstWhere('id', $selectedDeviceId) ?? null;
            if ($selectedDevice) {
                session(['selected_device_id' => $selectedDevice['id']]);
            }
        } else {
            session()->forget('selected_device_id');
        }

        // ===== 1. MENU SIDEBAR (PROGRESSIVE DISCLOSURE SESUAI PILIHAN PERANGKAT & ROLE) =====
        if (! $selectedDevice) {
            // BELUM MEMILIH PERANGKAT (Halaman "Perangkat Saya"):
            // Sidebar HANYA "Perangkat Saya" & "Logout".
            $menus = [
                ['label' => 'Perangkat Saya',   'icon' => 'bi-hdd-network',     'url' => route('dashboard'), 'active' => true],
                ['label' => 'Logout',           'icon' => 'bi-box-arrow-right', 'url' => route('logout'),    'active' => false],
            ];
        } else {
            // SUDAH MEMILIH PERANGKAT (masuk ke monitoring kolam):
            // Menu bersama (Shared): Dashboard, Riwayat Sensor, Inferensi Fuzzy
            $menus = [
                ['label' => 'Perangkat Saya',   'icon' => 'bi-hdd-network',     'url' => route('dashboard'), 'active' => false],
                ['label' => 'Dashboard',        'icon' => 'bi-speedometer2',   'url' => route('dashboard', ['device' => $selectedDevice['id']]), 'active' => true],
                ['label' => 'Riwayat Sensor',   'icon' => 'bi-clock-history',  'url' => route('riwayat-sensor'), 'active' => false],
                ['label' => 'Inferensi Fuzzy',  'icon' => 'bi-diagram-3',      'url' => route('inferensi-fuzzy'), 'active' => false],
            ];
        // ===== Menu sidebar =====
        $menus = $this->getSidebarMenus();

            // Menu khusus Teknisi: Kontrol Aktuator manual
            if ($userRole === 'teknisi') {
                $menus[] = ['label' => 'Kontrol Aktuator', 'icon' => 'bi-sliders', 'url' => route('kontrol-aktuator'), 'active' => false];
            }

            $menus[] = ['label' => 'Logout', 'icon' => 'bi-box-arrow-right', 'url' => route('logout'), 'active' => false];
        }

        // Data monitoring spesifik (untuk tampilan detail perangkat)
        $kualitas = [
            'status' => $selectedDevice['kualitas'] ?? 'BAIK',
            'indeks' => $selectedDevice['indeks'] ?? 75.3,
            'sub'    => [
                ['label' => 'Indeks Kimiawi', 'nilai' => 72],
                ['label' => 'Indeks Osmotik', 'nilai' => 78],
                ['label' => 'Indeks Fisik',   'nilai' => 76],
            ],
        ];

        $parameters = [
            ['nama' => 'Derajat Keasaman', 'nilai' => $selectedDevice['ph'] ?? '8,48',      'satuan' => 'pH',   'status' => 'normal',  'persen' => 70],
            ['nama' => 'Oksigen Terlarut', 'nilai' => $selectedDevice['do'] ?? '5,40',      'satuan' => 'mg/L', 'status' => 'cukup',   'persen' => 50],
            ['nama' => 'Suhu Air',         'nilai' => $selectedDevice['suhu_raw'] ?? '32,4', 'satuan' => '°C',  'status' => 'waspada', 'persen' => 82],
            ['nama' => 'Level Air',        'nilai' => '78',                                  'satuan' => '%',   'status' => 'normal',  'persen' => 78],
            ['nama' => 'Salinitas',        'nilai' => $selectedDevice['sal_raw'] ?? '30,2',  'satuan' => 'ppt', 'status' => 'normal',  'persen' => 64],
            ['nama' => 'Kekeruhan',        'nilai' => '12,5',                                'satuan' => 'NTU', 'status' => 'cukup',   'persen' => 45],
        ];

        $trends = [
            [
                'id' => 'ph', 'nama' => 'Derajat Keasaman', 'nilai' => ($selectedDevice['ph'] ?? '8,48') . ' pH', 'status' => 'normal',
                'min' => 7, 'max' => 9, 'step' => 1,
                'data' => [7.0,7.4,7.9,8.3,8.5,8.6,8.6,8.3,8.1,8.1,8.0,7.9,8.1,8.3,8.4,8.5,8.6,8.7,8.8,8.6,8.4,8.2,8.1,8.0,8.0],
            ],
            [
                'id' => 'do', 'nama' => 'Oksigen Terlarut', 'nilai' => ($selectedDevice['do'] ?? '5,40') . ' mg/L', 'status' => 'cukup',
                'min' => 4, 'max' => 8, 'step' => 2,
                'data' => [5.2,5.5,5.8,6.0,6.1,6.1,6.0,6.0,6.0,6.1,6.3,6.6,7.2,6.7,6.6,6.5,6.3,6.0,5.7,5.4,5.0,4.7,4.4,4.2,4.0],
            ],
            [
                'id' => 'suhu', 'nama' => 'Suhu Air', 'nilai' => ($selectedDevice['suhu'] ?? '32,4 °C'), 'status' => 'waspada',
                'min' => 25, 'max' => 35, 'step' => 5,
                'data' => [29.5,29,28.3,27.4,26.5,26,25.7,25.5,25.5,25.4,25.7,26.5,27.6,29.3,30.8,32,33,33.2,33.3,33.3,33,32.8,32.2,31.4,31.5],
            ],
            [
                'id' => 'level', 'nama' => 'Level Air', 'nilai' => '78%', 'status' => 'normal',
                'min' => 50, 'max' => 100, 'step' => 25,
                'data' => [52,60,70,82,90,94,95,93,76,70,72,78,85,90,92,91,86,78,70,66,64,62,68,74,72],
            ],
            [
                'id' => 'salinitas', 'nama' => 'Salinitas', 'nilai' => ($selectedDevice['salinitas'] ?? '30,2 ppt'), 'status' => 'normal',
                'min' => 25, 'max' => 35, 'step' => 5,
                'data' => [29,29.4,29.8,30,30.3,30.6,30.8,30.5,30.2,30,29.8,29.9,30.1,30.4,30.7,31,30.8,30.5,30.3,30.1,30,30.2,30.3,30.2,30.2],
            ],
            [
                'id' => 'turbid', 'nama' => 'Kekeruhan', 'nilai' => '12,5 NTU', 'status' => 'cukup',
                'min' => 0, 'max' => 20, 'step' => 10,
                'data' => [8,8.5,9,10,11,12,13,14,15,14,13,12.5,12,11,10.5,11,12,13,14,13.5,13,12.8,12.6,12.5,12.5],
            ],
        ];

        $alerts = array_fill(0, 3, [
            'tipe'   => 'ALERT KRITIS',
            'lokasi' => $selectedDevice['nama'] ?? 'Tambak A',
            'pesan'  => 'ALERT System: pH ' . ($selectedDevice['nama'] ?? 'Tambak A') . ' mencapai 8.9 (Kritis).',
        ]);

        return view('dashboard', compact(
            'userRole', 'menus', 'devices', 'totalDevices', 'onlineCount', 'offlineCount',
            'optimalCount', 'warningCount', 'avgQuality',
            'selectedDevice', 'kualitas', 'parameters', 'trends', 'alerts'
        ));
    }

    /**
     * Tambah Perangkat Baru (Hanya Teknisi / Admin)
     */
    public function storeDevice(Request $request)
    {
        if (session('user_role', 'teknisi') === 'nelayan') {
            return redirect()->back()->with('pesan_warning', 'Akses ditolak: Nelayan tidak memiliki izin untuk mendaftarkan perangkat baru.');
        }

        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $devices = $this->getDevices();
        $nextNum = count($devices) + 1;
        $randomCode = strtoupper(substr(md5(uniqid()), 0, 4));

        $newDevice = [
            'id'        => 'AQ-BOX-' . chr(64 + min($nextNum, 26)) . $nextNum,
            'token'     => 'CRAB-TOKEN-' . $randomCode,
            'nama'      => $request->input('nama'),
            'lokasi'    => $request->input('lokasi') ?: 'Tambak Utama',
            'petambak'  => 'Pak Robi (Petambak Binaan)',
            'status'    => 'online',
            'last_seen' => 'Baru saja',
            'kualitas'  => 'BAIK',
            'indeks'    => 78.5,
            'ph'        => '8,20',
            'suhu'      => '29,0 °C',
            'suhu_raw'  => '29,0',
            'do'        => '5,80',
            'salinitas' => '25,5 ppt',
            'sal_raw'   => '25,5',
        ];

        // Tambahkan ke urutan paling atas
        array_unshift($devices, $newDevice);
        session(['devices' => $devices, 'has_device' => true]);

        return redirect()->route('dashboard')->with('pesan_sukses', "Wadah {$newDevice['nama']} ({$newDevice['id']}) berhasil ditambahkan! Salin API Key untuk menghubungkan node telemetri.");
    }

    /**
     * Edit / Perbarui Informasi Perangkat (Hanya Teknisi / Admin)
     */
    public function updateDevice(Request $request, $id)
    {
        if (session('user_role', 'teknisi') === 'nelayan') {
            return redirect()->back()->with('pesan_warning', 'Akses ditolak: Nelayan tidak memiliki izin untuk mengubah data perangkat.');
        }

        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $devices = $this->getDevices();

        foreach ($devices as &$device) {
            if ($device['id'] === $id) {
                $device['nama'] = $request->input('nama');
                if ($request->filled('lokasi')) {
                    $device['lokasi'] = $request->input('lokasi');
                }
                break;
            }
        }

        session(['devices' => $devices]);

        return redirect()->back()->with('pesan_sukses', "Informasi wadah {$id} berhasil diperbarui.");
    }

    /**
     * Hapus Perangkat (Hanya Teknisi / Admin)
     */
    public function destroyDevice($id)
    {
        if (session('user_role', 'teknisi') === 'nelayan') {
            return redirect()->back()->with('pesan_warning', 'Akses ditolak: Nelayan tidak memiliki izin untuk menghapus perangkat.');
        }

        $devices = $this->getDevices();
        $devices = array_values(array_filter($devices, fn ($d) => $d['id'] !== $id));

        session([
            'devices'    => $devices,
            'has_device' => count($devices) > 0,
        ]);

        return redirect()->route('dashboard')->with('pesan_sukses', "Wadah {$id} telah dihapus.");
    }

    /**
     * Ambil data perangkat dari sesi (default awal: 4 wadah budidaya sesuai sistem binaan teknisi tambak)
     */
    private function getDevices(): array
    {
        if (! session()->has('devices') || count(session('devices')) < 4) {
            $defaultDevices = [
                [
                    'id'        => 'AQ-BOX-A1',
                    'token'     => 'CRAB-TOKEN-8F2A',
                    'nama'      => 'Unit 01 — Apartemen Kepiting Soka A1',
                    'lokasi'    => 'Sektor Utara Tambak A1',
                    'status'    => 'online',
                    'last_seen' => '10 detik lalu',
                    'kualitas'  => 'BAIK',
                    'indeks'    => 75.3,
                    'ph'        => '8,18',
                    'suhu'      => '29,5 °C',
                    'suhu_raw'  => '29,5',
                    'do'        => '5,76',
                    'salinitas' => '25,1 ppt',
                    'sal_raw'   => '25,1',
                ],
                [
                    'id'        => 'AQ-BOX-B2',
                    'token'     => 'CRAB-TOKEN-3C9D',
                    'nama'      => 'Unit 02 — Kolam Pembesaran B2',
                    'lokasi'    => 'Sektor Barat Tambak B',
                    'status'    => 'online',
                    'last_seen' => '30 detik lalu',
                    'kualitas'  => 'SANGAT BAIK',
                    'indeks'    => 82.0,
                    'ph'        => '8,40',
                    'suhu'      => '28,8 °C',
                    'suhu_raw'  => '28,8',
                    'do'        => '6,10',
                    'salinitas' => '26,5 ppt',
                    'sal_raw'   => '26,5',
                ],
                [
                    'id'        => 'AQ-BOX-C1',
                    'token'     => 'CRAB-TOKEN-7E1B',
                    'nama'      => 'Unit 03 — Kolam Karantina & Aklimatisasi C1',
                    'lokasi'    => 'Bangsal Riset & Karantina',
                    'status'    => 'online',
                    'last_seen' => '1 menit lalu',
                    'kualitas'  => 'CUKUP',
                    'indeks'    => 68.5,
                    'ph'        => '7,45',
                    'suhu'      => '33,2 °C',
                    'suhu_raw'  => '33,2',
                    'do'        => '4,30',
                    'salinitas' => '22,4 ppt',
                    'sal_raw'   => '22,4',
                ],
                [
                    'id'        => 'AQ-BOX-D4',
                    'token'     => 'CRAB-TOKEN-5A8F',
                    'nama'      => 'Unit 04 — Wadah Jerigen Adaptasi D4',
                    'lokasi'    => 'Sektor Selatan Tambak D',
                    'status'    => 'online',
                    'last_seen' => '3 menit lalu',
                    'kualitas'  => 'WASPADA',
                    'indeks'    => 54.2,
                    'ph'        => '7,20',
                    'suhu'      => '34,0 °C',
                    'suhu_raw'  => '34,0',
                    'do'        => '3,90',
                    'salinitas' => '21,0 ppt',
                    'sal_raw'   => '21,0',
                ],
            ];
            session(['devices' => $defaultDevices, 'has_device' => true]);
            return $defaultDevices;
        }

        return session('devices', []);
    }
}
