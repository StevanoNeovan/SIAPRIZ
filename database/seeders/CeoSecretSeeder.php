<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;

class CeoSecretSeeder extends Seeder
{
    public function run()
    {
        // Ambil semua user dengan role CEO (id_role = 2)
        $ceos = Pengguna::where('id_role', 2)->get();

        foreach ($ceos as $ceo) {
            $ceo->ceo_secret = Hash::make('SIAPRIZ-CEO-2026');
            $ceo->save();
        }
    }
}
