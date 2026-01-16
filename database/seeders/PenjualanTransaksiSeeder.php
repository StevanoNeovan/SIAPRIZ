<?php
// database/seeders/PenjualanTransaksiSeeder.php
// UPDATED: Sesuai dengan schema database yang baru

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenjualanTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        // Generate data for last 3 months
        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();
        
        $transactionId = 1;
        $detailId = 1;
        
        // Get all products
        $products = DB::table('produk')->get();
        
        if ($products->isEmpty()) {
            $this->command->error('No products found! Run ProdukSeeder first.');
            return;
        }
        
        // Loop through dates
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Random 5-15 transactions per day
            $transactionsPerDay = rand(5, 15);
            
            for ($i = 0; $i < $transactionsPerDay; $i++) {
                $marketplaceId = rand(1, 4); // Random marketplace
                $marketplaceNames = ['Shopee', 'Tokopedia', 'Lazada', 'Umum'];
                $marketplaceName = $marketplaceNames[$marketplaceId - 1];
                
                // Generate order ID (sesuai kolom 'order_id' bukan 'nomor_order')
                $orderId = $marketplaceName . '-' . $date->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
                
                // Random products (1-3 products per transaction)
                $numProducts = rand(1, 3);
                $totalPesanan = 0;
                $productItems = [];
                
                for ($p = 0; $p < $numProducts; $p++) {
                    $product = $products->random();
                    $quantity = rand(1, 3);
                    
                    // Harga satuan (random variation ±10% dari harga dasar)
                    $hargaSatuan = $product->harga_dasar * (1 + (rand(-10, 10) / 100));
                    $subtotal = $hargaSatuan * $quantity;
                    $totalPesanan += $subtotal;
                    
                    $productItems[] = [
                        'id_produk' => $product->id_produk,
                        'sku' => $product->sku,
                        'nama_produk' => $product->nama_produk,
                        'variasi' => null, // Could add random variations if needed
                        'quantity' => $quantity,
                        'harga_satuan' => $hargaSatuan,
                        'subtotal' => $subtotal,
                    ];
                }
                
                // Calculate fees (sesuai schema baru)
                $ongkosKirim = rand(10000, 50000);
                $totalDiskon = rand(0, 1) ? rand(5000, 50000) : 0; // 50% chance
                $biayaKomisi = $totalPesanan * (rand(3, 8) / 100); // 3-8% komisi marketplace
                
                $pendapatanBersih = $totalPesanan - $totalDiskon + $ongkosKirim - $biayaKomisi;
                
                // Status order (90% selesai, 10% others)
                $statusOptions = array_merge(
                    array_fill(0, 9, 'selesai'),
                    ['dibatalkan']
                );
                $status = $statusOptions[rand(0, 9)];
                
                // Generate customer data
                $cities = ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Makassar', 'Palembang', 'Tangerang', 'Depok', 'Bekasi'];
                $provinces = ['DKI Jakarta', 'Jawa Timur', 'Jawa Barat', 'Sumatera Utara', 'Jawa Tengah', 'Sulawesi Selatan', 'Sumatera Selatan', 'Banten'];
                
                $city = $cities[array_rand($cities)];
                $province = $provinces[array_rand($provinces)];
                
                // Insert transaction (FIXED COLUMN NAMES)
                DB::table('penjualan_transaksi')->insert([
                    'id_transaksi' => $transactionId,
                    'id_perusahaan' => 1,
                    'id_marketplace' => $marketplaceId,
                    'order_id' => $orderId, // FIXED: order_id not nomor_order
                    'tanggal_order' => $date->copy()->setTime(rand(8, 20), rand(0, 59)),
                    'status_order' => $status,
                    'total_pesanan' => $totalPesanan, // FIXED: total_pesanan not total_harga
                    'total_diskon' => $totalDiskon, // FIXED: total_diskon not diskon
                    'ongkos_kirim' => $ongkosKirim,
                    'biaya_komisi' => $biayaKomisi, // FIXED: biaya_komisi not biaya_admin
                    'pendapatan_bersih' => $status === 'selesai' ? $pendapatanBersih : 0,
                    'nama_customer' => 'Customer-' . rand(1000, 9999),
                    'kota_customer' => $city,
                    'provinsi_customer' => $province,
                    'id_batch_upload' => null,
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ]);
                
                // Insert transaction details
                foreach ($productItems as $item) {
                    DB::table('penjualan_transaksi_detail')->insert([
                        'id_detail' => $detailId++,
                        'id_transaksi' => $transactionId,
                        'id_produk' => $item['id_produk'],
                        'sku' => $item['sku'],
                        'nama_produk' => $item['nama_produk'],
                        'variasi' => $item['variasi'],
                        'quantity' => $item['quantity'],
                        'harga_satuan' => $item['harga_satuan'],
                        'subtotal' => $item['subtotal'],
                        'dibuat_pada' => now(),
                    ]);
                }
                
                $transactionId++;
            }
        }
        
        $this->command->info('✓ Generated ' . ($transactionId - 1) . ' transactions');
        $this->command->info('✓ Generated ' . ($detailId - 1) . ' transaction details');
    }
}