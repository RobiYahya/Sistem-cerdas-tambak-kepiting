
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// ===== Auth (tampilan saja) =====
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ===== Dashboard =====
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Menu lain masih placeholder (hanya tampilan). Arahkan ke dashboard dulu.
foreach (['riwayat-sensor', 'inferensi-fuzzy', 'kontrol-aktuator', 'notifikasi-alarm', 'kalibrasi-sensor', 'pengaturan'] as $slug) {
    Route::get("/{$slug}", [DashboardController::class, 'index'])->name($slug);
}