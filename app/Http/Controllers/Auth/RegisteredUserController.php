<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Perusahaan;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Show register page
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Process SIAPRIZ registration
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'bidang_usaha'    => 'required|string|max:255',
            'email'           => ['required', 'email', 'max:255', 'unique:pengguna,email'],
            'password'        => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 1️⃣ Buat perusahaan
        $perusahaan = Perusahaan::create([
            'nama_perusahaan' => $request->nama_perusahaan,
            'bidang_usaha'    => $request->bidang_usaha,
        ]);

        // 2️⃣ Buat owner perusahaan (pengguna)
        $pengguna = Pengguna::create([
            'id_perusahaan' => $perusahaan->id_perusahaan,
            'id_role'       => 1, // CEO / Owner (sesuaikan id role kamu)
            'nama_lengkap'  => $request->username,
            'username' => $request->username,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'is_aktif'      => 0,
        ]);

        // 3️⃣ Kirim email verifikasi
        event(new Registered($pengguna));

        session(['pending_verification_user' => $pengguna->id_pengguna]);

        return redirect()->route('verification.notice');
    }
}
