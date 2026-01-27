<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Perusahaan;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display login form
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login attempt
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('username', 'password');
        $credentials['is_aktif'] = true; // Only allow active users

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Update last login timestamp
            Auth::user()->update([
                'login_terakhir' => now()
            ]);

            // Cek apakah sudah ada profil usaha aktif
            $profilAda = Perusahaan::where('is_aktif', true)->exists();

            if (!$profilAda) {
                return redirect()->route('profil-usaha.create')
                    ->with('success', 'Silakan lengkapi profil usaha terlebih dahulu.');
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang, ' . Auth::user()->nama_lengkap);
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}
