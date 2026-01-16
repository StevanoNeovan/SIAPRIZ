<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('role')->insert([
            [
                'id_role' => 1,
                'nama_role' => 'Administrator',
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ],
            [
                'id_role' => 2,
                'nama_role' => 'CEO',
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ],
        ]);
    }
}