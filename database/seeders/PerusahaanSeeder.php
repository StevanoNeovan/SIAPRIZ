<?php
// database/seeders/PerusahaanSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerusahaanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('perusahaan')->insert([
            [
                'id_perusahaan' => 1,
                'nama_perusahaan' => 'Toko Elektronik Jaya',
                'logo_url' => null,
                'bidang_usaha' => 'Elektronik & Gadget',
                'is_aktif' => true,
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ],
        ]);
    }
}