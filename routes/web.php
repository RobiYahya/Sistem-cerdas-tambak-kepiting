<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
Route::get('/', function () {
    return view('welcome');
});


Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Menu lain masih placeholder (hanya tampilan). Arahkan ke dashboard dulu.
foreach (['riwayat-sensor', 'inferensi-fuzzy', 'kontrol-aktuator', 'notifikasi-alarm', 'kalibrasi-sensor', 'pengaturan', 'logout'] as $slug) {
    Route::get("/{$slug}", [DashboardController::class, 'index'])->name($slug);
}