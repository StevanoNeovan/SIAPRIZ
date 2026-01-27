<?php
// app/Services/Parser/Contracts/ColumnMapperInterface.php

namespace App\Services\Parser\Contracts;

/**
 * Interface untuk mapping kolom dari berbagai format marketplace
 * ke format standard SIAPRIZ
 */
interface ColumnMapperInterface
{
    /**
     * Get required columns yang harus ada di file
     * 
     * @return array
     */
    public function getRequiredColumns(): array;
    
    /**
     * Get mapping dari kolom file ke kolom standard
     * Format: ['standard_column' => 'file_column']
     * 
     * @return array
     */
    public function getColumnMapping(): array;
    
    /**
     * Get kolom untuk grouping order (biasanya order ID)
     * 
     * @return string
     */
    public function getOrderIdColumn(): string;
    
    /**
     * Get kolom untuk tanggal order
     * Bisa array jika ada prioritas (gunakan yang pertama ada)
     * 
     * @return string|array
     */
    public function getDateColumns(): array;
    
    /**
     * Get kolom untuk status order
     * 
     * @return string
     */
    public function getStatusColumn(): string;
}
