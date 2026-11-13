<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Menu sidebar terpusat untuk seluruh aplikasi AquaCrab.
     */
    protected function getSidebarMenus()
    {
        return [
            ['label' => 'Dashboard',          'icon' => 'bi-house',             'route' => 'dashboard'],
            ['label' => 'Riwayat Sensor',     'icon' => 'bi-clock-history',     'route' => 'riwayat-sensor'],
            [
                'label' => 'Inferensi Fuzzy',
                'icon'  => 'bi-circle-half',
                'svg'   => '<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-svg"><circle cx="12" cy="12" r="9.5"></circle><line x1="12" y1="2.5" x2="12" y2="21.5"></line></svg>',
                'route' => 'inferensi-fuzzy'
            ],
            ['label' => 'Kontrol Aktuator',   'icon' => 'bi-sliders',           'route' => 'kontrol-aktuator'],

            // ===== Menu berikut di-comment karena belum memiliki halaman fisik =====
            // ['label' => 'Notifikasi & Alarm', 'icon' => 'bi-alarm',             'route' => 'notifikasi-alarm'],
            // ['label' => 'Kalibrasi Sensor',   'icon' => 'bi-file-earmark-text', 'route' => 'kalibrasi-sensor'],
            // ['label' => 'Pengaturan',         'icon' => 'bi-gear',              'route' => 'pengaturan'],

            ['label' => 'Logout',             'icon' => 'bi-box-arrow-right',   'route' => 'logout'],
        ];
    }
}
