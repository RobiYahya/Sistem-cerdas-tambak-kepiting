
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RiwayatSensorController;
use App\Http\Controllers\KontrolAktuatorController;
use App\Http\Controllers\NotifikasiController;
use Illuminate\Support\Facades\Route;

// ===== Auth (tampilan saja) =====
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/switch-role', [AuthController::class, 'switchRole'])->name('switch-role');

// ===== Dashboard =====
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/perangkat/tambah', [DashboardController::class, 'storeDevice'])->name('perangkat.store');
Route::post('/perangkat/update/{id}', [DashboardController::class, 'updateDevice'])->name('perangkat.update');
Route::post('/perangkat/delete/{id}', [DashboardController::class, 'destroyDevice'])->name('perangkat.destroy');

// ===== Riwayat Sensor =====
Route::get('/riwayat-sensor', [RiwayatSensorController::class, 'index'])->name('riwayat-sensor');
Route::get('/riwayat-sensor/export-csv', [RiwayatSensorController::class, 'exportCsv'])->name('riwayat-sensor.csv');
Route::get('/riwayat-sensor/export-pdf', [RiwayatSensorController::class, 'exportPdf'])->name('riwayat-sensor.pdf');

// ===== Kontrol Aktuator =====
Route::get('/kontrol-aktuator', [KontrolAktuatorController::class, 'index'])->name('kontrol-aktuator');

// ===== Pusat Notifikasi & Peringatan Wadah =====
Route::get('/notifikasi-wadah', [NotifikasiController::class, 'index'])->name('notifikasi-wadah');

// Menu lain masih placeholder (hanya tampilan). Arahkan ke dashboard dulu.
foreach (['inferensi-fuzzy', 'notifikasi-alarm', 'kalibrasi-sensor', 'pengaturan'] as $slug) {
    Route::get("/{$slug}", [DashboardController::class, 'index'])->name($slug);
}