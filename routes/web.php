<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AdminPasswordController;
use App\Http\Controllers\Auth\CeoPasswordController;
use Illuminate\Support\Facades\URL;
use App\Models\Pengguna;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingPageController::class, 'index'])->name('landing');

/*
|--------------------------------------------------------------------------
| Guest (Belum Login)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthenticatedSessionController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'login']);

    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store']);

    // ===========================
    // CEO Lupa Password (Self-service)
    // ===========================
    Route::get('/ceo/forgot-password', [\App\Http\Controllers\Auth\CeoPasswordController::class, 'show'])
        ->name('ceo.password.forgot');

    Route::post('/ceo/reset-password', [\App\Http\Controllers\Auth\CeoPasswordController::class, 'reset'])
        ->name('ceo.password.reset');

    // ===========================
    // Admin Lupa Password (Self-service)
    // ===========================
    Route::get('/admin/forgot-password', [\App\Http\Controllers\Auth\AdminPasswordController::class, 'showForgotForm'])
        ->name('admin.password.forgot');

    Route::post('/admin/send-reset-link', [\App\Http\Controllers\Auth\AdminPasswordController::class, 'sendResetLink'])
        ->name('admin.password.send');

    Route::get('/admin/reset-password/{token}', [\App\Http\Controllers\Auth\AdminPasswordController::class, 'showResetForm'])
        ->name('admin.password.reset.form');

    Route::post('/admin/reset-password', [\App\Http\Controllers\Auth\AdminPasswordController::class, 'resetPassword'])
        ->name('admin.password.update');
});

/*
|--------------------------------------------------------------------------
| Email Verification (TANPA HARUS LOGIN)
|--------------------------------------------------------------------------
*/

// Halaman info "cek email kamu"
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

// Klik link dari email
Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {

    $user = Pengguna::findOrFail($id);

    if (! hash_equals(sha1($user->email), $hash)) {
        abort(403);
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified(); // 🔥 INI PENTING
        event(new \Illuminate\Auth\Events\Verified($user));
    }

    Auth::logout();

    return redirect()->route('login')
        ->with('success', 'Email berhasil diverifikasi. Silakan login.');
})->middleware('signed')->name('verification.verify');


/*
|--------------------------------------------------------------------------
| Kirim ulang email verifikasi
|--------------------------------------------------------------------------
*/

Route::post('/email/verification-notification', function () {

    $userId = session('pending_verification_user');

    if (! $userId) {
        return redirect()->route('login')
            ->with('error', 'Session verifikasi habis. Silakan daftar ulang.');
    }

    $user = Pengguna::findOrFail($userId);

    $user->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');

})->middleware(['throttle:6,1'])->name('verification.send');


/*
|--------------------------------------------------------------------------
| Authenticated + Verified
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified.admin'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    // NEW: Product detail endpoint
    Route::get('/dashboard/product/{id}/details', [DashboardController::class, 'showProductDetails'])
        ->name('dashboard.product.details');

    Route::get('/infografis', [\App\Http\Controllers\InfografisController::class, 'index'])
        ->name('infografis.index');
     
    // NEW: Download report route
    Route::get('/dashboard/download-report', [DashboardController::class, 'downloadReport'])
        ->name('dashboard.download-report');

    /*
    |--------------------------------------------------------------------------
    | Administrator Only
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Administrator')->group(function () {

        Route::get('/penjualan/upload', [\App\Http\Controllers\UploadPenjualanController::class, 'index'])
            ->name('penjualan.upload');

        Route::get('/penjualan/template', [\App\Http\Controllers\UploadPenjualanController::class, 'downloadTemplate'])
            ->name('penjualan.template');

        Route::post('/penjualan/upload', [\App\Http\Controllers\UploadPenjualanController::class, 'store'])
            ->name('penjualan.upload.store');

        Route::get('/penjualan/upload/{id}', [\App\Http\Controllers\UploadPenjualanController::class, 'show'])
            ->name('penjualan.upload-detail');

        Route::get('/penjualan/upload/{id}/download', [\App\Http\Controllers\UploadPenjualanController::class, 'downloadFile'])
            ->name('penjualan.download');

        // Profil Usaha
        Route::get('/profil-usaha', [\App\Http\Controllers\ProfilUsahaController::class, 'index'])
            ->name('profil-usaha.index');

        Route::get('/profil-usaha/create', [\App\Http\Controllers\ProfilUsahaController::class, 'create'])
            ->name('profil-usaha.create');

        Route::post('/profil-usaha', [\App\Http\Controllers\ProfilUsahaController::class, 'store'])
            ->name('profil-usaha.store');

        Route::get('/profil-usaha/edit', [\App\Http\Controllers\ProfilUsahaController::class, 'edit'])
            ->name('profil-usaha.edit');

        Route::post('/profil-usaha/update', [\App\Http\Controllers\ProfilUsahaController::class, 'update'])
            ->name('profil-usaha.update');

        Route::post('/profil-usaha/remove-logo', [\App\Http\Controllers\ProfilUsahaController::class, 'removeLogo'])
            ->name('profil-usaha.remove-logo');
    });
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
