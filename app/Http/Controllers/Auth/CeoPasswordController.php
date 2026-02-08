<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CeoPasswordController extends Controller
{
    /**
     * Tampilkan form reset password CEO
     */
    public function show()
    {
        return view('auth.ceo-reset-password'); // buat blade khusus CEO
    }

    /**
     * Proses reset password CEO
     */
    public function reset(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        // Ambil pengguna berdasarkan email + id_role CEO
        $ceoRole = \App\Models\Role::where('nama_role', 'ceo')->first();

        if (!$ceoRole) {
            throw ValidationException::withMessages([
                'email' => ['Role CEO belum ada di database.'],
            ]);
        }

        $ceo = Pengguna::where('email', $request->email)
            ->where('id_role', $ceoRole->id_role)
            ->first();

        if (!$ceo) {
            throw ValidationException::withMessages([
                'email' => ['Email CEO tidak ditemukan.'],
            ]);
        }

        // Update password
        $ceo->password = Hash::make($request->password);
        $ceo->save();

        return redirect()->route('login')->with('success', 'Password berhasil diubah.');
    }
}
