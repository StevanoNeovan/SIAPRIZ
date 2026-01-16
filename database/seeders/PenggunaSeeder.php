<?php
// database/seeders/PenggunaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pengguna')->insert([
            [
                'id_pengguna' => 1,
                'id_perusahaan' => 1,
                'id_role' => 1, // Administrator
                'username' => 'admin',
                'email' => 'admin@siapriz.com',
                'password' => Hash::make('123456'),
                'nama_lengkap' => 'Stevano Neovan',
                'is_aktif' => true,
                'login_terakhir' => null,
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ],
            [
                'id_pengguna' => 2,
                'id_perusahaan' => 1,
                'id_role' => 2, // CEO
                'username' => 'ceo',
                'email' => 'ceo@siapriz.com',
                'password' => Hash::make('123456'),
                'nama_lengkap' => 'Alung Destantio',
                'is_aktif' => true,
                'login_terakhir' => null,
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ],
        ]);
    }
}