<?php
// database/seeders/MarketplaceSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('marketplace')->insert([
            [
                'id_marketplace' => 1,
                'nama_marketplace' => 'Shopee',
                'kode_marketplace' => 'SHOPEE',
                'is_aktif' => true,
                'dibuat_pada' => now(),
                
            ],
            [
                'id_marketplace' => 2,
                'nama_marketplace' => 'Tokopedia',
                'kode_marketplace' => 'TOKOPEDIA',
                'is_aktif' => true,
                'dibuat_pada' => now(),
                
            ],
            [
                'id_marketplace' => 3,
                'nama_marketplace' => 'Lazada',
                'kode_marketplace' => 'LAZADA',
                'is_aktif' => true,
                'dibuat_pada' => now(),
                
            ],
            [
                'id_marketplace' => 4,
                'nama_marketplace' => 'Umum',
                'kode_marketplace' => 'UMUM',
                'is_aktif' => true,
                'dibuat_pada' => now(),
                
            ],
        ]);
    }
}