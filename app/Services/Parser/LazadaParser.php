<?php
// app/Services/Parser/LazadaParser.php

namespace App\Services\Parser;

use App\Services\Parser\Contracts\ColumnMapperInterface;
use App\Services\Parser\Contracts\StatusMapperInterface;
use App\Services\Parser\Mappers\LazadaColumnMapper;
use App\Services\Parser\Mappers\LazadaStatusMapper;
use Illuminate\Support\Collection;

/**
 * Lazada Parser - untuk format CSV asli Lazada
 * UPDATED: Hanya ambil paidPrice sebagai total_pesanan
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
     * Override parseOrderFinancialData untuk Lazada
     * UPDATED: Aggregate paidPrice dari semua rows untuk total_pesanan
     */
    protected function parseOrderFinancialData(
        Collection $orderRows,
        ColumnMapperInterface $columnMapper
    ): array {
        $mapping = $columnMapper->getColumnMapping();

        $totalPesanan = 0;
        $totalDiskon = 0;
        $ongkosKirim = 0;

        foreach ($orderRows as $row) {
            $row = $row instanceof Collection ? $row->toArray() : $row;

            // Aggregate paidPrice
            $totalPesanan += $this->parseDecimal(
                $this->getColumnValue($row, $mapping['total_pesanan'])
            );

            // Aggregate discount
            $totalDiskon += $this->parseDecimal(
                $this->getColumnValue($row, $mapping['total_diskon'])
            );
        }

        // Ongkir dari row pertama
        $firstRow = $orderRows->first()->toArray();
        $ongkosKirim = $this->parseDecimal(
            $this->getColumnValue($firstRow, $mapping['ongkos_kirim'])
        );

        return [
            'total_pesanan' => $totalPesanan,  // Total dari semua items
            'total_diskon' => $totalDiskon,
            'ongkos_kirim' => $ongkosKirim,
        ];
    }
    
    /**
     * Override parseItem untuk custom logic Lazada
     */
    protected function parseItem(array $row, ColumnMapperInterface $columnMapper): ?array
    {
        // Lazada file mungkin tidak memiliki quantity terpisah
        $quantity = $this->parseInt(
            $this->getColumnValue($row, 'quantity') ?: 
            $this->getColumnValue($row, 'Quantity') ?: 
            $this->getColumnValue($row, 'qty') ?: 
            1 
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