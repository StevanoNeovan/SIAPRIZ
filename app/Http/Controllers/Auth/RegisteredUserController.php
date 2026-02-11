<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Perusahaan;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
        // ✅ Validasi dulu sebelum apapun
        try {
            $validated = $request->validate([
                'nama_perusahaan' => ['required', 'string', 'max:255'],
                'bidang_usaha'    => ['required', 'in:Jasa,Dagang,Manufaktur,Lainnya'],
                'jenis_usaha'     => ['required', 'string', 'max:255'],
                'username'        => ['required', 'string', 'max:255', 'unique:pengguna,username'],
                'email'           => ['required', 'email', 'max:255', 'unique:pengguna,email'],
                'password'        => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Redirect kembali dengan error
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        }

        try {
            // Gunakan database transaction untuk memastikan data konsisten
            DB::beginTransaction();

            // 1️⃣ Buat perusahaan
            $perusahaan = Perusahaan::create([
                'nama_perusahaan' => $validated['nama_perusahaan'],
                'bidang_usaha'    => $validated['bidang_usaha'],
                'jenis_usaha'     => $validated['jenis_usaha'],
            ]);

            // 2️⃣ Buat owner perusahaan (pengguna)
            $pengguna = Pengguna::create([
                'id_perusahaan' => $perusahaan->id_perusahaan,
                'id_role'       => 1,
                'nama_lengkap'  => $validated['username'],
                'username'      => $validated['username'],
                'email'         => $validated['email'],
                'password'      => Hash::make($validated['password']),
                'is_aktif'      => 0,
            ]);

            DB::commit();

            // 3️⃣ Kirim email verifikasi
            event(new Registered($pengguna));

            session(['pending_verification_user' => $pengguna->id_pengguna]);

            return redirect()->route('verification.notice');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log error untuk debugging
            \Log::error('Registration Error: ' . $e->getMessage());
            
            // Redirect dengan pesan error umum
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.']);
        }
    }
}