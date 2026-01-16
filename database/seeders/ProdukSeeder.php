<?php
// database/seeders/ProdukSeeder.php
// FIXED: harga_dasar not harga_jual

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['SKU-001', 'Smartphone Samsung Galaxy A54', 'Smartphone', 4500000],
            ['SKU-002', 'Laptop ASUS VivoBook 14', 'Laptop', 7800000],
            ['SKU-003', 'Headphone Sony WH-1000XM5', 'Audio', 4200000],
            ['SKU-004', 'Smart TV LG 43 Inch', 'Elektronik', 5500000],
            ['SKU-005', 'Apple AirPods Pro Gen 2', 'Audio', 3800000],
            ['SKU-006', 'Mouse Logitech MX Master 3', 'Aksesoris', 1200000],
            ['SKU-007', 'Keyboard Mechanical Keychron K2', 'Aksesoris', 1500000],
            ['SKU-008', 'Powerbank Anker 20000mAh', 'Aksesoris', 450000],
            ['SKU-009', 'Smartwatch Apple Watch Series 9', 'Wearable', 6500000],
            ['SKU-010', 'Tablet iPad Air 5th Gen', 'Tablet', 8900000],
            ['SKU-011', 'Speaker JBL Flip 6', 'Audio', 1800000],
            ['SKU-012', 'Webcam Logitech C920', 'Aksesoris', 1100000],
            ['SKU-013', 'SSD External Samsung T7 1TB', 'Storage', 1900000],
            ['SKU-014', 'Monitor Dell 27 Inch 4K', 'Monitor', 4800000],
            ['SKU-015', 'Drone DJI Mini 3 Pro', 'Gadget', 8500000],
        ];

        foreach ($products as $index => $product) {
            DB::table('produk')->insert([
                'id_produk' => $index + 1,
                'id_perusahaan' => 1,
                'sku' => $product[0],
                'nama_produk' => $product[1],
                'kategori' => $product[2],
                'harga_dasar' => $product[3], // FIXED: harga_dasar not harga_jual
                'is_aktif' => true,
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ]);
        }
        
        $this->command->info('✓ Seeded 15 products');
    }
}