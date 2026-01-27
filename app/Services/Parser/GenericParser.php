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
}
