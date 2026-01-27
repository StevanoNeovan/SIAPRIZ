<?php
// app/Services/Parser/TokopediaParser.php

namespace App\Services\Parser;

use App\Services\Parser\Contracts\ColumnMapperInterface;
use App\Services\Parser\Contracts\StatusMapperInterface;
use App\Services\Parser\Mappers\TokopediaColumnMapper;
use App\Services\Parser\Mappers\TokopediaStatusMapper;

/**
 * Tokopedia Parser - untuk format CSV asli Tokopedia
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
     */
    protected function parseFinancialData(array $row, ColumnMapperInterface $columnMapper): array
    {
        // Parse financial data
        $orderAmount = $this->parseDecimal($this->getColumnValue($row, 'Order Amount'));
        $orderRefund = $this->parseDecimal($this->getColumnValue($row, 'Order Refund Amount'));
        
        // Shipping
        $originalShippingFee = $this->parseDecimal($this->getColumnValue($row, 'Original Shipping Fee'));
        $shippingFeeAfterDiscount = $this->parseDecimal($this->getColumnValue($row, 'Shipping Fee After Discount'));
        $shippingFeePlatformDiscount = $this->parseDecimal($this->getColumnValue($row, 'Shipping Fee Platform Discount'));
        $shippingFeeSellerDiscount = $this->parseDecimal($this->getColumnValue($row, 'Shipping Fee Seller Discount'));
        $shippingInsurance = $this->parseDecimal($this->getColumnValue($row, 'Shipping Insurance'));
        
        // Discount & Fees
        $platformDiscount = $this->parseDecimal($this->getColumnValue($row, 'SKU Platform Discount'));
        $sellerDiscount = $this->parseDecimal($this->getColumnValue($row, 'SKU Seller Discount'));
        $paymentPlatformDiscount = $this->parseDecimal($this->getColumnValue($row, 'Payment platform discount'));
        
        $buyerServiceFee = $this->parseDecimal($this->getColumnValue($row, 'Buyer Service Fee'));
        $handlingFee = $this->parseDecimal($this->getColumnValue($row, 'Handling Fee'));
        
        // Calculate totals
        $totalDiskon = $platformDiscount + $sellerDiscount + $paymentPlatformDiscount;
        
        // Biaya komisi = ????
        $biayaKomisi = $handlingFee + $buyerServiceFee;
        
        // Total pesanan = order amount (yang dibayar customer)
        $totalPesanan = $orderAmount;
        
        // Ongkir yang dibayar customer
        $ongkosKirim = $shippingFeeAfterDiscount;
        
        // Pendapatan bersih = order amount - biaya komisi - refund
        $pendapatanBersih = $orderAmount - $biayaKomisi - $orderRefund;
        
        return [
            'total_pesanan' => $totalPesanan,
            'total_diskon' => $totalDiskon,
            'ongkos_kirim' => $ongkosKirim,
            'biaya_komisi' => $biayaKomisi,
            'pendapatan_bersih' => $pendapatanBersih,
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
        
        return [
            'sku' => $this->cleanString($this->getColumnValue($row, 'Seller SKU')) ?: 
                     $this->cleanString($this->getColumnValue($row, 'SKU ID')) ?: 
                     'TOKPED-' . uniqid(),
            'nama_produk' => $this->cleanString($this->getColumnValue($row, 'Product Name')),
            'variasi' => $this->cleanString($this->getColumnValue($row, 'Variation')),
            'quantity' => $quantity,
            'harga_satuan' => $skuUnitPrice,
            'subtotal' => $skuSubtotalAfterDiscount, // Subtotal setelah diskon
        ];
    }
}
