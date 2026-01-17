<?php
// app/Services/Parser/Mappers/ShopeeStatusMapper.php

namespace App\Services\Parser\Mappers;

use App\Services\Parser\Contracts\StatusMapperInterface;

/**
 * Status mapper untuk Shopee
 */
class ShopeeStatusMapper implements StatusMapperInterface
{
    public function mapStatus(string $status): string
    {
        $status = strtolower($status);
        
        $mapping = [
            'selesai' => 'selesai',
            'completed' => 'selesai',
            'delivered' => 'selesai',
            'sedang dikirim' => 'proses',
            'shipping' => 'proses',
            'siap dikirim' => 'proses',
            'ready to ship' => 'proses',
            'dikemas' => 'proses',
            'processing' => 'proses',
            'dibatalkan' => 'dibatalkan',
            'cancelled' => 'dibatalkan',
            'batal' => 'dibatalkan',
            'pengembalian/penukaran barang' => 'dikembalikan',
            'returned' => 'dikembalikan',
            'dikembalikan' => 'dikembalikan',
        ];
        
        return $mapping[$status] ?? 'proses';
    }
}
