<?php
// app/Services/Parser/Mappers/ShopeeColumnMapper.php

namespace App\Services\Parser\Mappers;

use App\Services\Parser\Contracts\ColumnMapperInterface;

/**
 * Column mapper untuk format CSV Shopee asli
 * UPDATED: Hapus biaya_komisi dan pendapatan_bersih
 */
class ShopeeColumnMapper implements ColumnMapperInterface
{
    public function getRequiredColumns(): array
    {
        return [
            'No. Pesanan',
            'Status Pesanan',
            'Nama Produk',
            'Jumlah',
            'Total Pembayaran',
        ];
    }
    
    public function getColumnMapping(): array
    {
        return [
            'order_id' => 'No. Pesanan',
            'status_order' => 'Status Pesanan',
            'sku' => 'SKU Induk',
            'nama_produk' => 'Nama Produk',
            'variasi' => 'Nama Variasi',
            'quantity' => 'Jumlah',
            'harga_satuan' => 'Harga Awal',
            'total_pesanan' => 'Total Pembayaran',
            'total_diskon' => 'Total Diskon',
            'ongkos_kirim' => 'Ongkos Kirim Dibayar oleh Pembeli',
            'nama_customer' => 'Nama Penerima',
            'kota_customer' => 'Kota/Kabupaten',
            'provinsi_customer' => 'Provinsi',
        ];
    }
    
    public function getOrderIdColumn(): string
    {
        return 'No. Pesanan';
    }
    
    public function getDateColumns(): array
    {
        return [
            'Waktu Pesanan Selesai',
            'Waktu Pesanan Dibuat',
        ];
    }
    
    public function getStatusColumn(): string
    {
        return 'Status Pesanan';
    }
}