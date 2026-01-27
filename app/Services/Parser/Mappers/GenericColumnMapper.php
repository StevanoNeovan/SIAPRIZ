<?php
// app/Services/Parser/Mappers/GenericColumnMapper.php

namespace App\Services\Parser\Mappers;

use App\Services\Parser\Contracts\ColumnMapperInterface;

/**
 * Column mapper untuk template SIAPRIZ universal
 * Semua marketplace menggunakan template yang sama
 */
class GenericColumnMapper implements ColumnMapperInterface
{
    public function getRequiredColumns(): array
    {
        return [
            'No. Pesanan',
            'Tanggal Order',
            'Status Order',
            'SKU',
            'Nama Produk',
            'Variasi',
            'Jumlah',
            'Harga Satuan',
            'Total Pesanan',
            'Total Diskon',
            'Ongkos Kirim',
            'Biaya Komisi',
            'Pendapatan Bersih',
            'Nama Customer',
            'Kota',
            'Provinsi',
        ];
    }
    
    public function getColumnMapping(): array
    {
        return [
            'order_id' => 'No. Pesanan',
            'tanggal_order' => 'Tanggal Order',
            'status_order' => 'Status Order',
            'sku' => 'SKU',
            'nama_produk' => 'Nama Produk',
            'variasi' => 'Variasi',
            'quantity' => 'Jumlah',
            'harga_satuan' => 'Harga Satuan',
            'total_pesanan' => 'Total Pesanan',
            'total_diskon' => 'Total Diskon',
            'ongkos_kirim' => 'Ongkos Kirim',
            'biaya_komisi' => 'Biaya Komisi',
            'pendapatan_bersih' => 'Pendapatan Bersih',
            'nama_customer' => 'Nama Customer',
            'kota_customer' => 'Kota',
            'provinsi_customer' => 'Provinsi',
        ];
    }
    
    public function getOrderIdColumn(): string
    {
        return 'No. Pesanan';
    }
    
    public function getDateColumns(): array
    {
        return ['Tanggal Order'];
    }
    
    public function getStatusColumn(): string
    {
        return 'Status Order';
    }
}
