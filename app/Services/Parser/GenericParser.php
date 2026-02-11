<?php
// app/Services/Parser/GenericParser.php

namespace App\Services\Parser;

use App\Services\Parser\Contracts\ColumnMapperInterface;
use App\Services\Parser\Contracts\StatusMapperInterface;
use App\Services\Parser\Mappers\GenericColumnMapper;
use App\Services\Parser\Mappers\GenericStatusMapper;

/**
 * Generic Parser - untuk template universal SIAPRIZ
 * Semua marketplace pakai template yang sama
 */
class GenericParser extends AbstractParser
{
    protected function getColumnMapper(): ColumnMapperInterface
    {
        return new GenericColumnMapper();
    }
    
    protected function getStatusMapper(): StatusMapperInterface
    {
        return new GenericStatusMapper();
    }
    
    public function getMarketplaceCode(): string
    {
        return 'GENERIC';
    }


    protected function parseItem(array $row, ColumnMapperInterface $columnMapper): ?array
    {
        $quantity = $this->parseInt($this->getColumnValue($row, 'Jumlah'));
        $hargaSatuan = $this->parseDecimal($this->getColumnValue($row, 'Harga Satuan'));

        return [
            'sku' => $this->cleanString($this->getColumnValue($row, 'SKU')) ?: 'GEN-' . uniqid(),
            'nama_produk' => $this->cleanString($this->getColumnValue($row, 'Nama Produk')),
            'variasi' => $this->cleanString($this->getColumnValue($row, 'Variasi')),
            'quantity' => $quantity,
            'harga_satuan' => $hargaSatuan,
            'subtotal' => $quantity * $hargaSatuan,
        ];
    }
}
