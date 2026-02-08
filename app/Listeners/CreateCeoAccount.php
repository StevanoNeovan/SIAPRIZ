<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\CeoAccountCreated;
use Illuminate\Support\Str;

class CreateCeoAccount
{
    public function handle(Verified $event)
    {
    $admin = $event->user;

    if ($admin->id_role != 1) return;

    if (Pengguna::where('id_perusahaan', $admin->id_perusahaan)
        ->where('id_role', 2)->exists()) {
        return;
    }

    $password = Str::random(10);

    $ceo = Pengguna::create([
        'id_perusahaan' => $admin->id_perusahaan,
        'id_role'       => 2,
        'nama_lengkap'  => 'CEO ' . $admin->perusahaan->nama_perusahaan,
        'username'      => 'ceo@' . Str::slug($admin->perusahaan->nama_perusahaan) . '.com',
        'email'         => 'ceo@' . Str::slug($admin->perusahaan->nama_perusahaan) . '.com',
        'password'      => Hash::make($password),
        'is_aktif'      => true,
        'email_verified_at' => now(),
    ]);

    Mail::to($admin->email)->send(
        new CeoAccountCreated($ceo, $password)
    );
    }

}
