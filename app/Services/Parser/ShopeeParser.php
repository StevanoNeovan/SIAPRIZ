<?php
// app/Services/Parser/ShopeeParser.php
// ENHANCED with debug logging

namespace App\Services\Parser;

use Illuminate\Support\Collection;
use App\Services\Parser\Contracts\ColumnMapperInterface;
use App\Services\Parser\Contracts\StatusMapperInterface;
use App\Services\Parser\Mappers\ShopeeColumnMapper;
use App\Services\Parser\Mappers\ShopeeStatusMapper;

/**
 * Shopee Parser - untuk format CSV asli Shopee
 * ENHANCED: Debug logging untuk trace price parsing
 */
class ShopeeParser extends AbstractParser
{
    protected function getColumnMapper(): ColumnMapperInterface
    {
        return new ShopeeColumnMapper();
    }
    
    protected function getStatusMapper(): StatusMapperInterface
    {
        return new ShopeeStatusMapper();
    }
    
    public function getMarketplaceCode(): string
    {
        return 'SHOPEE';
    }
    
    /**
     * Override parseFinancialData untuk custom logic Shopee
     * ENHANCED: Extensive debug logging
     */
    protected function parseFinancialData(array $row, ColumnMapperInterface $columnMapper): array
    {
        // Get raw values
        $rawTotalPembayaran = $this->getColumnValue($row, 'Total Pembayaran');
        $rawTotalDiskon = $this->getColumnValue($row, 'Total Diskon');
        $rawOngkosKirim = $this->getColumnValue($row, 'Ongkos Kirim Dibayar oleh Pembeli');
        
        \Illuminate\Support\Facades\Log::debug('ShopeeParser: Raw financial values', [
            'Total Pembayaran' => $rawTotalPembayaran,
            'Total Diskon' => $rawTotalDiskon,
        ]);
        
        // Parse values
        $totalPembayaran = $this->parseDecimal($rawTotalPembayaran);
        $ongkosKirim = $this->parseDecimal($rawOngkosKirim);
        
        // Total diskon
        $totalDiskon = $this->parseDecimal($rawTotalDiskon);
        
        $result = [
            'total_pesanan' => $totalPembayaran,
            'total_diskon' => $totalDiskon,
            'ongkos_kirim' => $ongkosKirim,
        ];
        
        \Illuminate\Support\Facades\Log::debug('ShopeeParser: Parsed financial data', [
            'total_pesanan' => $totalPembayaran,
            'total_diskon' => $totalDiskon,
            'ongkos_kirim' => $ongkosKirim,
        ]);
        
        return $result;
    }
    
    /**
     * Override parseItem untuk custom logic Shopee
     * ENHANCED: Debug logging
     */
    protected function parseItem(array $row, ColumnMapperInterface $columnMapper): ?array
    {
        $rawQuantity = $this->getColumnValue($row, 'Jumlah');
        $rawHargaSetelahDiskon = $this->getColumnValue($row, 'Harga Setelah Diskon');
        $rawTotalHargaProduk = $this->getColumnValue($row, 'Total Pembayaran');
        $rawSKU = $this->getColumnValue($row, 'SKU Induk');
        $rawNamaProduk = $this->getColumnValue($row, 'Nama Produk');
        
        \Illuminate\Support\Facades\Log::debug('ShopeeParser: Raw item values', [
            'SKU' => $rawSKU,
            'Nama Produk' => $rawNamaProduk,
            'Jumlah' => $rawQuantity,
            'Harga Setelah Diskon' => $rawHargaSetelahDiskon,
            'Total Pembayaran' => $rawTotalHargaProduk,
        ]);
        
        $quantity = $this->parseInt($rawQuantity);
        $hargaSetelahDiskon = $this->parseDecimal($rawHargaSetelahDiskon);
        $totalHargaProduk = $this->parseDecimal($rawTotalHargaProduk);
        
        $item = [
            'sku' => $this->cleanString($rawSKU) ?: 
                     $this->cleanString($this->getColumnValue($row, 'Nomor Referensi SKU')) ?: 
                     'SHOPEE-' . uniqid(),
            'nama_produk' => $this->cleanString($rawNamaProduk),
            'variasi' => $this->cleanString($this->getColumnValue($row, 'Nama Variasi')),
            'quantity' => $quantity,
            'harga_satuan' => $hargaSetelahDiskon,
            'subtotal' => $totalHargaProduk,
        ];
        
        \Illuminate\Support\Facades\Log::debug('ShopeeParser: Parsed item', $item);
        
        return $item;
    }
}