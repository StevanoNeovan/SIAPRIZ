<?php
// app/Services/Parser/Mappers/TokopediaStatusMapper.php

namespace App\Services\Parser\Mappers;

use App\Services\Parser\Contracts\StatusMapperInterface;

/**
 * Status mapper untuk Tokopedia
 */
class TokopediaStatusMapper implements StatusMapperInterface
{
    public function mapStatus(string $status): string
    {
        $status = strtolower($status);
        
        $mapping = [
            'delivered' => 'selesai',
            'finished' => 'selesai',
            'completed' => 'selesai',
            'shipped' => 'proses',
            'processed' => 'proses',
            'on process' => 'proses',
            'ready to ship' => 'proses',
            'awaiting pickup' => 'proses',
            'cancelled' => 'dibatalkan',
            'canceled' => 'dibatalkan',
            'returned' => 'dikembalikan',
            'refunded' => 'dikembalikan',
        ];
        
        return $mapping[$status] ?? 'proses';
    }
}
