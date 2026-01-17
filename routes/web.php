<?php
// routes/web.php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes (Not Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [AuthenticatedSessionController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');
    
    // Dashboard - accessible by both Administrator & CEO
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Infografis - accessible by both roles
    Route::get('/infografis', [\App\Http\Controllers\InfografisController::class, 'index'])
        ->name('infografis.index');
    
    // Administrator only routes
    Route::middleware(['role:Administrator'])->group(function () {
        // Upload penjualan
        Route::get('/penjualan/upload', [\App\Http\Controllers\UploadPenjualanController::class, 'index'])
            ->name('penjualan.upload');
        Route::post('/penjualan/upload', [\App\Http\Controllers\UploadPenjualanController::class, 'store'])
            ->name('penjualan.upload.store');
         Route::get('/penjualan/upload/{id}', [\App\Http\Controllers\UploadPenjualanController::class, 'show'])
            ->name('penjualan.upload-detail');
        
        // Profil Usaha
        Route::get('/profil-usaha', [\App\Http\Controllers\ProfilUsahaController::class, 'edit'])
            ->name('profil-usaha.edit');
        Route::put('/profil-usaha', [\App\Http\Controllers\ProfilUsahaController::class, 'update'])
            ->name('profil-usaha.update');
    });
    
    
});