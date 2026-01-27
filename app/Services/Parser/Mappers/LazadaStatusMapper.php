<?php
// app/Services/Parser/Mappers/LazadaStatusMapper.php

namespace App\Services\Parser\Mappers;

use App\Services\Parser\Contracts\StatusMapperInterface;

/**
 * Status mapper untuk Lazada
 */
class LazadaStatusMapper implements StatusMapperInterface
{
    public function mapStatus(string $status): string
    {
        $status = strtolower($status);
        
        $mapping = [
            'completed' => 'selesai',
            'delivered' => 'selesai',
            'selesai' => 'selesai',
            'success' => 'selesai',
            'ready_to_ship' => 'selesai',
            'pending' => 'proses',
            'processing' => 'proses',
            'proses' => 'proses',
            'cancelled' => 'dibatalkan',
            'canceled' => 'dibatalkan',
            'dibatalkan' => 'dibatalkan',
            'refunded' => 'dikembalikan',
            'returned' => 'dikembalikan',
            'dikembalikan' => 'dikembalikan',
        ];
        
        return $mapping[$status] ?? 'proses';
    }
}
