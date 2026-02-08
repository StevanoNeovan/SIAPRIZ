<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use App\Models\Pengguna;

class AktifkanPenggunaSetelahVerifikasi
{
    public function handle(Verified $event): void
    {
        $user = $event->user;

        if ($user instanceof Pengguna) {
            $user->update([
                'is_aktif' => 1
            ]);
        }
    }
}
