<?php
// app/Services/Parser/TokopediaParser.php

namespace App\Services\Parser;

use App\Services\Parser\Contracts\ColumnMapperInterface;
use App\Services\Parser\Contracts\StatusMapperInterface;
use App\Services\Parser\Mappers\TokopediaColumnMapper;
use App\Services\Parser\Mappers\TokopediaStatusMapper;

/**
 * Tokopedia Parser - untuk format CSV asli Tokopedia
 * UPDATED: Hanya ambil Order Amount sebagai total_pesanan
 */
class TokopediaParser extends AbstractParser
{
    protected function getColumnMapper(): ColumnMapperInterface
    {
        return new TokopediaColumnMapper();
    }
    
    protected function getStatusMapper(): StatusMapperInterface
    {
        return new TokopediaStatusMapper();
    }
    
    public function getMarketplaceCode(): string
    {
        return 'TOKOPEDIA';
    }
    
    /**
     * Override parseFinancialData untuk custom logic Tokopedia
     * UPDATED: Hanya ambil Order Amount sebagai total_pesanan
     */
    protected function parseFinancialData(array $row, ColumnMapperInterface $columnMapper): array
    {
        // Order Amount = yang dibayar customer (pendapatan kotor)
        $orderAmount = $this->parseDecimal($this->getColumnValue($row, 'Order Amount'));
        
        // Shipping
        $shippingFeeAfterDiscount = $this->parseDecimal($this->getColumnValue($row, 'Shipping Fee After Discount'));
        
        // Discount (untuk informasi)
        $platformDiscount = $this->parseDecimal($this->getColumnValue($row, 'SKU Platform Discount'));
        $sellerDiscount = $this->parseDecimal($this->getColumnValue($row, 'SKU Seller Discount'));
        $paymentPlatformDiscount = $this->parseDecimal($this->getColumnValue($row, 'Payment platform discount'));
        
        $totalDiskon = $platformDiscount + $sellerDiscount + $paymentPlatformDiscount;
        
        return [
            'total_pesanan' => $orderAmount,  // Pendapatan kotor
            'total_diskon' => $totalDiskon,
            'ongkos_kirim' => $shippingFeeAfterDiscount,
        ];
    }
    
    /**
     * Override parseItem untuk custom logic Tokopedia
     */
    protected function parseItem(array $row, ColumnMapperInterface $columnMapper): ?array
    {
        $quantity = $this->parseInt($this->getColumnValue($row, 'Quantity'));
        $skuUnitPrice = $this->parseDecimal($this->getColumnValue($row, 'SKU Unit Original Price'));
        $skuSubtotalAfterDiscount = $this->parseDecimal($this->getColumnValue($row, 'SKU Subtotal After Discount'));
        $buyerServiceFee = $this->parseDecimal($this->getColumnValue($row, 'Buyer Service Fee'));
        $handlingFee = $this->parseDecimal($this->getColumnValue($row, 'Handling Fee'));
        
        return [
            'sku' => $this->cleanString($this->getColumnValue($row, 'Seller SKU')) ?: 
                     $this->cleanString($this->getColumnValue($row, 'SKU ID')) ?: 
                     'TOKPED-' . uniqid(),
            'nama_produk' => $this->cleanString($this->getColumnValue($row, 'Product Name')),
            'variasi' => $this->cleanString($this->getColumnValue($row, 'Variation')),
            'quantity' => $quantity,
            'harga_satuan' => $skuUnitPrice,
            'subtotal' => $skuSubtotalAfterDiscount + $buyerServiceFee + $handlingFee, // Subtotal setelah diskon + biaya layanan
        ];
    }
}