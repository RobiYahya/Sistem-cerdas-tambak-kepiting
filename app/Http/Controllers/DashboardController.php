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

        // Data monitoring spesifik (untuk tampilan detail perangkat sesuai mockup)
        $kualitas = [
            'status' => $selectedDevice['kualitas'] ?? 'BAIK',
            'indeks' => $selectedDevice['indeks'] ?? 75.5,
            'sub'    => [
                ['label' => 'Indeks Kimiawi', 'nilai' => 84],
                ['label' => 'Indeks Osmotik', 'nilai' => 54],
                ['label' => 'Indeks Fisik',   'nilai' => 92],
            ],
        ];

        // 6 Parameter probe sensor dengan nilai & fuzzifikasi
        $sixParameters = [
            [
                'id'     => 'ph',
                'label'  => 'pH',
                'nilai'  => '8,51',
                'satuan' => '',
                'fuzzy'  => 'Normal 0,633 • Basa 0,367',
                'status' => 'normal',
            ],
            [
                'id'     => 'do',
                'label'  => 'DO',
                'nilai'  => '5,71',
                'satuan' => 'mg/L',
                'fuzzy'  => 'Cukup 1,000',
                'status' => 'cukup',
            ],
            [
                'id'     => 'salinitas',
                'label'  => 'Salinitas',
                'nilai'  => '26,4',
                'satuan' => 'ppt',
                'fuzzy'  => 'Optimal 1,000',
                'status' => 'normal',
            ],
            [
                'id'     => 'suhu',
                'label'  => 'Suhu',
                'nilai'  => '32,4',
                'satuan' => '°C',
                'fuzzy'  => 'Optimal 0,067 • Panas 0,...',
                'status' => 'waspada',
            ],
            [
                'id'     => 'kekeruhan',
                'label'  => 'Kekeruhan',
                'nilai'  => '34',
                'satuan' => 'NTU',
                'fuzzy'  => 'Jernih 0,733',
                'status' => 'cukup',
            ],
            [
                'id'     => 'level',
                'label'  => 'Level Air',
                'nilai'  => '78',
                'satuan' => '%',
                'fuzzy'  => 'Normal 1,000',
                'status' => 'normal',
            ],
        ];

        // 4 Status aktuator wadah: Solenoid Valve, Aerator, Dosing Pump, dan Pompa Sirkulasi
        $aktuators = [
            [
                'id'    => 'solenoid-valve',
                'nama'  => 'Solenoid Valve',
                'sub'   => 'Regulasi Salinitas Air (Standby)',
                'state' => 'OFF',
            ],
            [
                'id'    => 'aerator',
                'nama'  => 'Aerator',
                'sub'   => 'Sirkulasi Oksigen Terlarut (Aktif)',
                'state' => 'ON',
            ],
            [
                'id'    => 'dosing-pump',
                'nama'  => 'Dosing Pump',
                'sub'   => 'Injeksi Buffer pH Air (Standby)',
                'state' => 'OFF',
            ],
            [
                'id'    => 'pompa-sirkulasi',
                'nama'  => 'Pompa Sirkulasi',
                'sub'   => 'Sirkulasi & Filtrasi Air (Aktif)',
                'state' => 'ON',
            ],
        ];

        // Data multi-parameter telemetri 24 jam (7 titik waktu: 00:00, 04:00, 08:00, 12:00, 16:00, 20:00, Sekarang)
        $telemetriTrend = [
            'labels'   => ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', 'Sekarang'],
            'datasets' => [
                [
                    'id'          => 'ph',
                    'label'       => 'pH (8,48)',
                    'data'        => [6.2, 6.4, 6.4, 6.5, 6.6, 6.1, 6.3],
                    'borderColor' => '#10b981',
                    'color'       => '#10b981',
                ],
                [
                    'id'          => 'do',
                    'label'       => 'DO (5,40 mg/L)',
                    'data'        => [6.9, 7.3, 7.5, 7.4, 7.3, 7.6, 7.7],
                    'borderColor' => '#0284c7',
                    'color'       => '#0284c7',
                ],
                [
                    'id'          => 'salinitas',
                    'label'       => 'Salinitas (26,4 ppt)',
                    'data'        => [7.5, 7.4, 7.3, 7.2, 7.1, 7.3, 7.6],
                    'borderColor' => '#8b5cf6',
                    'color'       => '#8b5cf6',
                ],
                [
                    'id'          => 'suhu',
                    'label'       => 'Suhu (32,4 °C)',
                    'data'        => [7.7, 7.8, 7.7, 7.5, 8.4, 7.2, 8.2],
                    'borderColor' => '#f97316',
                    'color'       => '#f97316',
                ],
            ],
        ];

        return view('dashboard', compact(
            'userRole', 'menus', 'devices', 'totalDevices', 'onlineCount', 'offlineCount',
            'optimalCount', 'warningCount', 'avgQuality',
            'selectedDevice', 'kualitas', 'sixParameters', 'aktuators', 'telemetriTrend'
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
                    'tipe'      => 'Apartemen Box Vertikal',
                    'lokasi'    => 'Sektor Utara Tambak A1',
                    'petambak'  => 'Pak Robi (Petambak Binaan)',
                    'populasi'  => '850 ekor',
                    'rssi'      => '-68 dBm',
                    'baterai'   => '94%',
                    'status'    => 'online',
                    'last_seen' => '10 detik lalu',
                    'kualitas'  => 'BAIK',
                    'indeks'    => 75.5,
                    'ph'        => '8,51',
                    'suhu'      => '32,4 °C',
                    'suhu_raw'  => '32,4',
                    'do'        => '5,71',
                    'salinitas' => '26,4 ppt',
                    'sal_raw'   => '26,4',
                ],
                [
                    'id'        => 'AQ-BOX-B2',
                    'token'     => 'CRAB-TOKEN-3C9D',
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
                    'tipe'      => 'Apartemen Box Vertikal',
                    'lokasi'    => 'Bangsal Riset & Karantina',
                    'petambak'  => 'Pak Budi Hartono',
                    'populasi'  => '120 ekor',
                    'rssi'      => '-79 dBm',
                    'baterai'   => '76%',
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
                    'tipe'      => 'Wadah Jerigen 20L',
                    'lokasi'    => 'Sektor Selatan Tambak D',
                    'petambak'  => 'Kelompok Tambak Sido Makmur',
                    'populasi'  => '45 ekor',
                    'rssi'      => '-82 dBm',
                    'baterai'   => '65%',
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
