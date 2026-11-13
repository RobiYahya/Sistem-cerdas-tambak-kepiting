<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InferensiFuzzyController extends Controller
{
    public function index()
    {
        $menus = $this->getSidebarMenus();

        $unitAktif = 'Unit 01 — Kolam Kepiting A';
        $units = [
            'Unit 01 — Kolam Kepiting A',
            'Unit 02 — Kolam Kepiting B',
            'Unit 03 — Kolam Kepiting C',
            'Unit 04 — Kolam Pembenihan',
        ];

        // Tahap 1: Fuzzifikasi
        $fuzzifikasi = [
            [
                'parameter' => 'pH',
                'nilai'     => '8,48',
                'satuan'    => '',
                'hasil'     => 'Normal 0,733 · Basa 0,133',
            ],
            [
                'parameter' => 'DO',
                'nilai'     => '5,40',
                'satuan'    => 'mg/L',
                'hasil'     => 'Cukup 1,000',
            ],
            [
                'parameter' => 'Salinitas',
                'nilai'     => '26,4',
                'satuan'    => 'ppt',
                'hasil'     => 'Optimal 1,000',
            ],
            [
                'parameter' => 'Suhu',
                'nilai'     => '32,4',
                'satuan'    => '°C',
                'hasil'     => 'Optimal 0,600 · Panas 0,200',
            ],
            [
                'parameter' => 'Kekeruhan',
                'nilai'     => '34',
                'satuan'    => 'NTU',
                'hasil'     => 'Jernih 0,867',
            ],
            [
                'parameter' => 'Level Air',
                'nilai'     => '78',
                'satuan'    => '%',
                'hasil'     => 'Normal 1,000',
            ],
        ];

        // Tahap 2: Indeks Sub Sistem
        $subSistem = [
            [
                'nama'     => 'Indeks Kimiawi',
                'subtitle' => 'pH + DO · FIS-1',
                'nilai'    => 78.6,
                'format'   => '78,6',
            ],
            [
                'nama'     => 'Indeks Osmotik',
                'subtitle' => 'Salinitas + Suhu · FIS-2',
                'nilai'    => 74.9,
                'format'   => '74,9',
            ],
            [
                'nama'     => 'Indeks Fisik',
                'subtitle' => 'Kekeruhan + Level Air · FIS-3',
                'nilai'    => 85.5,
                'format'   => '85,5',
            ],
        ];

        // Tahap 3: Aturan yang Aktif
        $aturanAktif = [
            [
                'id'     => 'R5',
                'alpha'  => 'α = 0,733',
                'status' => 'Aktif',
                'teks'   => 'IF pH Normal AND DO Cukup THEN IK Baik',
            ],
            [
                'id'     => 'R5',
                'alpha'  => 'α = 0,733',
                'status' => 'Aktif',
                'teks'   => 'IF pH Normal AND DO Cukup THEN IK Baik',
            ],
            [
                'id'     => 'R5',
                'alpha'  => 'α = 0,733',
                'status' => 'Aktif',
                'teks'   => 'IF pH Normal AND DO Cukup THEN IK Baik',
            ],
            [
                'id'     => 'R5',
                'alpha'  => 'α = 0,733',
                'status' => 'Aktif',
                'teks'   => 'IF pH Normal AND DO Cukup THEN IK Baik',
            ],
        ];

        // Tahap 4: Agregasi dan Centroid
        $agregasi = [
            'skor'   => 75.3,
            'format' => '75,3',
            'status' => 'BAIK',
        ];

        return view('inferensi-fuzzy', compact(
            'menus',
            'unitAktif',
            'units',
            'fuzzifikasi',
            'subSistem',
            'aturanAktif',
            'agregasi'
        ));
    }
}
