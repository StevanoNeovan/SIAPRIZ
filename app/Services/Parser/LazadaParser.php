<?php
// app/Services/Parser/LazadaParser.php

namespace App\Services\Parser;

use App\Services\Parser\Contracts\ColumnMapperInterface;
use App\Services\Parser\Contracts\StatusMapperInterface;
use App\Services\Parser\Mappers\LazadaColumnMapper;
use App\Services\Parser\Mappers\LazadaStatusMapper;

/**
 * Lazada Parser - untuk format CSV asli Lazada
 */
class LazadaParser extends AbstractParser
{
    protected function getColumnMapper(): ColumnMapperInterface
    {
        return new LazadaColumnMapper();
    }
    
    protected function getStatusMapper(): StatusMapperInterface
    {
        return new LazadaStatusMapper();
    }
    
    public function getMarketplaceCode(): string
    {
        return 'LAZADA';
    }
    
    /**
     * Override parseFinancialData untuk custom logic Lazada
     */
    protected function parseFinancialData(array $row, ColumnMapperInterface $columnMapper): array
    {
        // ===== Financial =====
        $totalPesanan = $this->parseDecimal(
            $this->getColumnValue($row, 'paidPrice')
        );

        $ongkosKirim = $this->parseDecimal(
            $this->getColumnValue($row, 'shippingFee')
        );

        $biayaKomisi = $this->parseDecimal(
            $this->getColumnValue($row, 'commission')
        );

        $voucher = $this->parseDecimal(
            $this->getColumnValue($row, 'sellerDiscountTotal')
        );

        $pendapatanBersih = $totalPesanan - $biayaKomisi - $voucher;
        
        return [
            'total_pesanan' => $totalPesanan,
            'total_diskon' => $voucher,
            'ongkos_kirim' => $ongkosKirim,
            'biaya_komisi' => $biayaKomisi,
            'pendapatan_bersih' => $pendapatanBersih,
        ];
    }
    
    /**
     * Override parseItem untuk custom logic Lazada
     */
    protected function parseItem(array $row, ColumnMapperInterface $columnMapper): ?array
    {
        $quantity = $this->parseInt(
            $this->getColumnValue($row, 'quantity')
        );

        $hargaSatuan = $this->parseDecimal(
            $this->getColumnValue($row, 'unitPrice')
        );

        return [
            'sku' => $this->cleanString(
                $this->getColumnValue($row, 'sellerSku')
            ) ?: 'LAZADA-' . uniqid(),

            'nama_produk' => $this->cleanString(
                $this->getColumnValue($row, 'itemName')
            ),

            'variasi' => $this->cleanString(
                $this->getColumnValue($row, 'variation')
            ),

            'quantity' => $quantity,
            'harga_satuan' => $hargaSatuan,
            'subtotal' => $quantity * $hargaSatuan,
        ];
    }
}
