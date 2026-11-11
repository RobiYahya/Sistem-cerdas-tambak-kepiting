<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        // Proteksi login
        if (! session('is_logged_in', false)) {
            return redirect()->route('login');
        }

        $userRole  = session('user_role', 'teknisi');
        $isTeknisi = ($userRole === 'teknisi');

        // ===== DAFTAR PERANGKAT (RBAC) =====
        $allDevices = [
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
                'indeks'    => '75,5',
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

        // Filter perangkat sesuai role
        if ($userRole === 'nelayan') {
            $devices = array_values(array_filter($allDevices, fn($d) => in_array($d['id'], ['AQ-BOX-A1', 'AQ-BOX-B2'])));
        } else {
            $devices = $allDevices;
        }

        // Perangkat yang dipilih — prioritas: query string → sesi → default pertama
        $selectedDeviceId = $request->query('device', session('selected_device_id'));
        $selectedDevice = $selectedDeviceId
            ? (collect($devices)->firstWhere('id', $selectedDeviceId) ?? $devices[0])
            : $devices[0];

        if ($selectedDevice) {
            session([
                'selected_device_id' => $selectedDevice['id'],
                'has_device'         => true,
            ]);
        }

        // ===== MENU SIDEBAR (untuk mobile nav) =====
        $menus = [
            ['label' => 'Perangkat Saya',   'icon' => 'bi-hdd-network',      'url' => route('dashboard'),                                              'active' => false],
            ['label' => 'Dashboard',        'icon' => 'bi-grid-fill',        'url' => route('dashboard', ['device' => $selectedDevice['id'] ?? '']),  'active' => false],
            ['label' => 'Analisis Kualitas Air', 'icon' => 'bi-water',      'url' => route('inferensi-fuzzy'),                                        'active' => false],
            ['label' => 'Riwayat & Laporan','icon' => 'bi-file-earmark-text','url' => route('riwayat-sensor'),                                        'active' => false],
            ['label' => 'Notifikasi Wadah', 'icon' => 'bi-bell',            'url' => route('notifikasi-wadah'),                                       'active' => true],
        ];

        if ($isTeknisi) {
            $menus[] = ['label' => 'Kontrol Aktuator', 'icon' => 'bi-sliders', 'url' => route('kontrol-aktuator'), 'active' => false];
        }

        $menus[] = ['label' => 'Logout', 'icon' => 'bi-box-arrow-right', 'url' => route('logout'), 'active' => false];

        // ===== STATISTIK PRIORITAS =====
        $stats = [
            'kritis'         => 1,
            'tinggi'         => 1,
            'sedang'         => 1,
            'rendah'         => 1,
            'butuh_tindakan' => 2,
        ];

        // ===== DAFTAR NOTIFIKASI =====
        $deviceNama = $selectedDevice['nama'] ?? 'Unit 01 — Apartemen Kepiting Soka A1';

        $notifikasiList = [
            [
                'id'          => 1,
                'level'       => 'kritis',
                'badge'       => 'PERINGATAN KRITIS',
                'badge_class' => 'pbadge-kritis',
                'card_class'  => 'notif-card-kritis',
                'wadah'       => $deviceNama,
                'waktu'       => '2 jam lalu',
                'judul'       => 'Derajat Keasaman (pH) Melebihi Batas Aman',
                'pemicu'      => 'pH: 8,92 > 8,50 pH',
                'deskripsi'   => 'Nilai pH air terdeteksi basa ekstrim 8,92. Dosing pump larutan penyangga asam telah dipicu otomatis oleh sistem inferensi fuzzy.',
                'status'      => 'pending',
                'konfirmasi'  => null,
            ],
            [
                'id'          => 2,
                'level'       => 'tinggi',
                'badge'       => 'PERINGATAN TINGGI',
                'badge_class' => 'pbadge-tinggi',
                'card_class'  => 'notif-card-tinggi',
                'wadah'       => $deviceNama,
                'waktu'       => '10 menit lalu',
                'judul'       => 'Suhu Permukaan Air Tambak Tinggi',
                'pemicu'      => '32,4 °C > 31,0 °C',
                'deskripsi'   => 'Suhu air mencapai 32,4°C. Mist Cooler telah dipicu otomatis oleh sistem inferensi fuzzy untuk mencegah stres termal pada kepiting.',
                'status'      => 'pending',
                'konfirmasi'  => null,
            ],
            [
                'id'          => 3,
                'level'       => 'sedang',
                'badge'       => 'PERINGATAN SEDANG',
                'badge_class' => 'pbadge-sedang',
                'card_class'  => 'notif-card-sedang',
                'wadah'       => $deviceNama,
                'waktu'       => '1 jam lalu',
                'judul'       => 'Oksigen Terlarut Mendekati Batas Kritis',
                'pemicu'      => 'DO: 5,40 mg/L',
                'deskripsi'   => 'Kadar DO berada pada 5,40 mg/L (cukup). Aerator Venturi dipertahankan aktif 85% duty cycle.',
                'status'      => 'confirmed',
                'konfirmasi'  => 'Telah dikonfirmasi oleh Pak Robi (Petambak) (45 menit lalu)',
            ],
            [
                'id'          => 4,
                'level'       => 'rendah',
                'badge'       => 'INFO RENDAH',
                'badge_class' => 'pbadge-rendah',
                'card_class'  => 'notif-card-rendah',
                'wadah'       => $deviceNama,
                'waktu'       => '3 jam lalu',
                'judul'       => 'Jadwal Pembersihan Filter Berkala',
                'pemicu'      => 'Operasi: 72 Jam',
                'deskripsi'   => 'Node telemetri mendeteksi akumulasi partikel bio-filter mendekati siklus pembersihan mingguan. Disarankan pembersihan dalam 24 jam ke depan.',
                'status'      => 'confirmed',
                'konfirmasi'  => 'Telah dikonfirmasi otomatis oleh sistem pemantauan',
            ],
        ];

        return view('notifikasi-wadah', compact(
            'userRole', 'isTeknisi', 'devices', 'selectedDevice',
            'menus', 'stats', 'notifikasiList'
        ));
    }
}
