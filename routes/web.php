
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RiwayatSensorController;
use App\Http\Controllers\KontrolAktuatorController;
use Illuminate\Support\Facades\Route;

// ===== Auth (tampilan saja) =====
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ===== Dashboard =====
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ===== Riwayat Sensor =====
Route::get('/riwayat-sensor', [RiwayatSensorController::class, 'index'])->name('riwayat-sensor');
Route::get('/riwayat-sensor/export-csv', [RiwayatSensorController::class, 'exportCsv'])->name('riwayat-sensor.csv');

// ===== Kontrol Aktuator =====
Route::get('/kontrol-aktuator', [KontrolAktuatorController::class, 'index'])->name('kontrol-aktuator');

// Menu lain masih placeholder (hanya tampilan). Arahkan ke dashboard dulu.
foreach (['inferensi-fuzzy', 'notifikasi-alarm', 'kalibrasi-sensor', 'pengaturan'] as $slug) {
    Route::get("/{$slug}", [DashboardController::class, 'index'])->name($slug);
}