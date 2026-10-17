<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    // Masih tampilan saja: belum ada validasi/autentikasi, langsung menuju dashboard.
    public function login(Request $request)
    {
        return redirect()->route('dashboard');
    }

    public function logout()
    {
        return redirect()->route('login');
    }
}