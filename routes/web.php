
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RiwayatSensorController;
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

// Menu lain masih placeholder (hanya tampilan). Arahkan ke dashboard dulu.
foreach (['inferensi-fuzzy', 'kontrol-aktuator', 'notifikasi-alarm', 'kalibrasi-sensor', 'pengaturan'] as $slug) {
    Route::get("/{$slug}", [DashboardController::class, 'index'])->name($slug);
}