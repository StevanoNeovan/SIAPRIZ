<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Pengguna;
use App\Mail\AdminPasswordResetMail;

class AdminPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.admin-forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:pengguna,email'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar dalam sistem'
        ]);

        $user = Pengguna::where('email', $request->email)->first();
        
        if (!$user->isAdministrator()) {
            return back()->withErrors([
                'email' => 'Email ini bukan akun Administrator'
            ]);
        }

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
            'expires_at' => now()->addHours(1)
        ]);

        try {
            Log::info('Attempting to send password reset email to: ' . $request->email);

            Mail::to($request->email)->send(new AdminPasswordResetMail($token, $user));
            
            Log::info('Password reset email sent successfully');
            
            return back()->with('success', 'Link reset password telah dikirim ke email Anda. Silakan cek inbox/spam.');
            
        } catch (\Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->withErrors([
                'email' => 'Gagal mengirim email: ' . $e->getMessage()
            ]);
        }
    }

    public function showResetForm($token)
    {
        $resetData = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if (!$resetData) {
            return redirect()->route('login')
                ->withErrors(['token' => 'Token reset password tidak valid']);
        }

        if (now()->greaterThan($resetData->expires_at)) {
            DB::table('password_reset_tokens')->where('token', $token)->delete();
            
            return redirect()->route('login')
                ->withErrors(['token' => 'Token reset password sudah kadaluarsa. Silakan request ulang.']);
        }

        return view('auth.admin-reset-password', [
            'token' => $token,
            'email' => $resetData->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok'
        ]);

        $resetData = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (!$resetData) {
            return back()->withErrors(['token' => 'Token tidak valid']);
        }

        if (now()->greaterThan($resetData->expires_at)) {
            DB::table('password_reset_tokens')->where('token', $request->token)->delete();
            return back()->withErrors(['token' => 'Token sudah kadaluarsa']);
        }

        $user = Pengguna::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'User tidak ditemukan']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        DB::table('log_audit')->insert([
            'id_pengguna' => $user->id_pengguna,
            'id_perusahaan' => $user->id_perusahaan,
            'jenis_aksi' => 'RESET_PASSWORD',
            'nama_tabel' => 'pengguna',
            'id_record' => $user->id_pengguna,
            'nilai_baru' => 'Password direset',
            'ip_address' => $request->ip(),
            'dibuat_pada' => now()
        ]);

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }
}