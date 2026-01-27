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
        $credentials['is_aktif'] = true; // hanya user aktif yang bisa login

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Update last login timestamp
            $user->update([
                'login_terakhir' => now()
            ]);

            // Hanya Administrator yang wajib isi profil usaha
            if ($user->isAdministrator()) {

                $profilAda = Perusahaan::where('is_aktif', true)->exists();

                if (!$profilAda) {
                    return redirect()->route('profil-usaha.create')
                        ->with('success', 'Silakan lengkapi profil usaha terlebih dahulu.');
                }
            }

            // Role lain (CEO dll) atau profil sudah ada → dashboard
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang, ' . $user->nama_lengkap);
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
