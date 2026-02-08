<?php
// app/Services/Parser/ShopeeParser.php

namespace App\Services\Parser;

use Illuminate\Support\Collection;
use App\Services\Parser\Contracts\ColumnMapperInterface;
use App\Services\Parser\Contracts\StatusMapperInterface;
use App\Services\Parser\Mappers\ShopeeColumnMapper;
use App\Services\Parser\Mappers\ShopeeStatusMapper;

/**
 * Shopee Parser - untuk format CSV asli Shopee
 * UPDATED: Hanya ambil total_pesanan (Total Pembayaran), abaikan komisi
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
     * UPDATED: Hanya ambil Total Pembayaran sebagai total_pesanan
     */
    protected function parseFinancialData(array $row, ColumnMapperInterface $columnMapper): array
    {
        // Total pembayaran = yang dibayar customer (pendapatan kotor)
        $totalPembayaran = $this->parseDecimal($this->getColumnValue($row, 'Total Pembayaran'));
        
        // Ongkir yang dibayar customer
        $ongkosKirim = $this->parseDecimal($this->getColumnValue($row, 'Ongkos Kirim Dibayar oleh Pembeli'));
        
        // Total diskon = semua jenis diskon (opsional, untuk informasi)
        $diskonPenjual = $this->parseDecimal($this->getColumnValue($row, 'Diskon Dari Penjual'));
        $diskonShopee = $this->parseDecimal($this->getColumnValue($row, 'Diskon Dari Shopee'));
        $voucherPenjual = $this->parseDecimal($this->getColumnValue($row, 'Voucher Ditanggung Penjual'));
        $voucherShopee = $this->parseDecimal($this->getColumnValue($row, 'Voucher Ditanggung Shopee'));
        $cashbackKoin = $this->parseDecimal($this->getColumnValue($row, 'Cashback Koin'));
        $potonganKoin = $this->parseDecimal($this->getColumnValue($row, 'Potongan Koin Shopee'));
        
        $totalDiskon = $diskonPenjual + $diskonShopee + $voucherPenjual + $voucherShopee + $cashbackKoin + $potonganKoin;
        
        return [
            'total_pesanan' => $totalPembayaran,  // Pendapatan kotor
            'total_diskon' => $totalDiskon,
            'ongkos_kirim' => $ongkosKirim,
        ];
    }
    
    /**
     * Override parseItem untuk custom logic Shopee
     */
    protected function parseItem(array $row, ColumnMapperInterface $columnMapper): ?array
    {
        $quantity = $this->parseInt($this->getColumnValue($row, 'Jumlah'));
        $hargaSetelahDiskon = $this->parseDecimal($this->getColumnValue($row, 'Harga Setelah Diskon'));
        $totalHargaProduk = $this->parseDecimal($this->getColumnValue($row, 'Total Harga Produk'));
        
        return [
            'sku' => $this->cleanString($this->getColumnValue($row, 'SKU Induk')) ?: 
                     $this->cleanString($this->getColumnValue($row, 'Nomor Referensi SKU')) ?: 
                     'SHOPEE-' . uniqid(),
            'nama_produk' => $this->cleanString($this->getColumnValue($row, 'Nama Produk')),
            'variasi' => $this->cleanString($this->getColumnValue($row, 'Nama Variasi')),
            'quantity' => $quantity,
            'harga_satuan' => $hargaSetelahDiskon, // Harga setelah diskon per item
            'subtotal' => $totalHargaProduk, // Total harga produk (qty * harga)
        ];
    }
}