<?php
// app/Services/Parser/Contracts/StatusMapperInterface.php

namespace App\Services\Parser\Contracts;

/**
 * Interface untuk mapping status dari berbagai marketplace
 * ke format standard SIAPRIZ
 */
interface StatusMapperInterface
{
    /**
     * Map status dari marketplace ke format standard
     * Standard format: 'selesai', 'proses', 'dibatalkan', 'dikembalikan'
     * 
     * @param string $status
     * @return string
     */
    public function mapStatus(string $status): string;
}
