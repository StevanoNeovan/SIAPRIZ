<?php
// database/seeders/PenjualanTransaksiSeeder.php

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
        
        // Loop through dates
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Random 5-15 transactions per day
            $transactionsPerDay = rand(5, 15);
            
            for ($i = 0; $i < $transactionsPerDay; $i++) {
                $marketplaceId = rand(1, 4); // Random marketplace
                $marketplaceName = ['Shopee', 'Tokopedia', 'Lazada', 'Blibli'][$marketplaceId - 1];
                
                // Generate order number
                $orderId = $marketplaceName . '-' . $date->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
                
                // Random products (1-3 products per transaction)
                $numProducts = rand(1, 3);
                $totalPesanan = 0;
                $products = [];
                
                for ($p = 0; $p < $numProducts; $p++) {
                    $productId = rand(1, 15);
                    $jumlah = rand(1, 3);
                    
                    // Get product price
                    $hargaDasar = DB::table('produk')
                        ->where('id_produk', $productId)
                        ->value('harga_dasar');
                    
                    $subtotal = $hargaDasar * $jumlah;
                    $totalPesanan += $subtotal;
                    
                    $products[] = [
                        'id_produk' => $productId,
                        'jumlah' => $jumlah,
                        'harga_dasar' => $hargaDasar,
                        'subtotal' => $subtotal,
                    ];
                }
                
                // Calculate fees
                $ongkosKirim = rand(10000, 50000);
                $biayaKomisi = $totalPesanan * (rand(2, 5) / 100); // 2-5% admin fee
                $diskon = rand(0, 1) ? rand(5000, 50000) : 0; // 50% chance of discount
                
                $pendapatanBersih = $totalPesanan + $ongkosKirim - $biayaKomisi - $diskon;
                
                // Status order (90% completed, 10% others)
                $statusOptions = ['selesai', 'selesai', 'selesai', 'selesai', 'selesai', 'selesai', 'selesai', 'selesai', 'selesai', 'dibatalkan'];
                $status = $statusOptions[rand(0, 9)];
                
                // Insert transaction
                DB::table('penjualan_transaksi')->insert([
                    'id_transaksi' => $transactionId,
                    'id_perusahaan' => 1,
                    'id_marketplace' => $marketplaceId,
                    'order_id' => $orderId,
                    'tanggal_order' => $date->copy()->setTime(rand(8, 20), rand(0, 59)),
                    'status_order' => $status,
                    'total_pesanan' => $totalPesanan,
                    'ongkos_kirim' => $ongkosKirim,
                    'biaya_komisi' => $biayaKomisi,
                    'total_diskon' => $diskon,
                    'pendapatan_bersih' => $status === 'selesai' ? $pendapatanBersih : 0,
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ]);
                

                // Insert transaction details
                foreach ($products as $product) {
                    DB::table('penjualan_transaksi_detail')->insert([
                        'id_detail' => $detailId++,
                        'id_transaksi' => $transactionId,
                        'id_produk' => $product['id_produk'],
                        'nama_produk' => DB::table('produk')->where('id_produk', $product['id_produk'])->value('nama_produk'),
                        'sku' =>  DB::table('produk')->where('id_produk', $product['id_produk'])->value('sku'),
                        'quantity' => $product['jumlah'],
                        'harga_satuan' => $product['harga_dasar'],
                        'subtotal' => $product['subtotal'],
                        'dibuat_pada' => now(),
                    ]);
                }
                
                $transactionId++;
            }
        }
        
        $this->command->info('Generated ' . ($transactionId - 1) . ' transactions with ' . ($detailId - 1) . ' transaction details');
    }
}