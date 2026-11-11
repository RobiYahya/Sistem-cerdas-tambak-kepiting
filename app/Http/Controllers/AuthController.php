<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Jika sudah login, langsung arahkan ke dashboard
        if (session('is_logged_in', false)) {
            return redirect()->route('dashboard');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        // Masuk ke sistem: default peran awal adalah Teknisi / Admin
        session([
            'is_logged_in' => true,
            'user_role'    => session('user_role', 'teknisi'),
            'has_device'   => true,
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Beralih peran antara Nelayan (Petambak Binaan) dan Teknisi / Admin
     */
    public function switchRole(Request $request)
    {
        $currentRole = session('user_role', 'teknisi');
        $newRole = ($currentRole === 'teknisi') ? 'nelayan' : 'teknisi';
        session(['user_role' => $newRole]);

        $roleLabel = ($newRole === 'teknisi') ? 'Teknisi / Admin' : 'Nelayan (Petambak Binaan)';
        return redirect()->back()->with('pesan_sukses', "Beralih peran menjadi {$roleLabel}. Tampilan menu dan hak akses telah disesuaikan.");
    }

    public function logout()
    {
        // Hapus seluruh session agar kembali ke awal
        session()->flush();

        return redirect()->route('login');
    }
}