<?php
// app/Services/Parser/ShopeeParser.php

namespace App\Services\Parser;

class ShopeeParser extends AbstractParser
{
    /**
     * Expected Shopee CSV columns (sesuai format asli)
     */
    private const REQUIRED_COLUMNS = [
        'No. Pesanan',
        'Status Pesanan',
        'Nama Produk',
        'Jumlah',
        'Total Pembayaran',
    ];
    
    private $header = [];
    
    public function getMarketplaceCode(): string
    {
        return 'SHOPEE';
    }
    
    public function validate(): bool
    {
        $data = $this->readFile();
        
        if ($data->isEmpty()) {
            return false;
        }
        
        // Store header for later use
        $this->header = $data->first()->toArray();
        
        // Check if header contains required columns
        foreach (self::REQUIRED_COLUMNS as $column) {
            if (!in_array($column, $this->header)) {
                return false;
            }
        }
        
        return true;
    }
    
    public function parse(): array
    {
        $data = $this->readFile();
        $transactions = [];
        $errors = [];
        
        // Store header
        $this->header = $data->first()->toArray();
        
        // Skip header row
        $rows = $data->skip(1);
        
        // Group by order ID (No. Pesanan)
        $groupedOrders = $rows->groupBy(function($row) {
            return $this->getColumnValue($row, 'No. Pesanan');
        });
        
        foreach ($groupedOrders as $orderId => $orderRows) {
            try {
                $firstRow = $orderRows->first();
                
                // Parse financial data (dari baris pertama order)
                $totalPembayaran = $this->parseDecimal($this->getColumnValue($firstRow, 'Total Pembayaran'));
                $ongkosKirim = $this->parseDecimal($this->getColumnValue($firstRow, 'Ongkos Kirim Dibayar oleh Pembeli'));
                $estimasiPotonganOngkir = $this->parseDecimal($this->getColumnValue($firstRow, 'Estimasi Potongan Biaya Pengiriman'));
                
                // Total diskon = semua jenis diskon
                $diskonPenjual = $this->parseDecimal($this->getColumnValue($firstRow, 'Diskon Dari Penjual'));
                $diskonShopee = $this->parseDecimal($this->getColumnValue($firstRow, 'Diskon Dari Shopee'));
                $voucherPenjual = $this->parseDecimal($this->getColumnValue($firstRow, 'Voucher Ditanggung Penjual'));
                $voucherShopee = $this->parseDecimal($this->getColumnValue($firstRow, 'Voucher Ditanggung Shopee'));
                $cashbackKoin = $this->parseDecimal($this->getColumnValue($firstRow, 'Cashback Koin'));
                $potonganKoin = $this->parseDecimal($this->getColumnValue($firstRow, 'Potongan Koin Shopee'));
                
                $totalDiskon = $diskonPenjual + $diskonShopee + $voucherPenjual + $voucherShopee + $cashbackKoin + $potonganKoin;
                
                // Biaya komisi = estimasi potongan ongkir (ini yang jadi beban seller)
                $biayaKomisi = $estimasiPotonganOngkir;
                
                // Total pesanan = total pembayaran (yang dibayar customer)
                $totalPesanan = $totalPembayaran;
                
                // Pendapatan bersih = total pembayaran - biaya komisi
                $pendapatanBersih = $totalPembayaran - $biayaKomisi;
                
                // Parse items (semua produk dalam order ini)
                $items = [];
                foreach ($orderRows as $row) {
                    $quantity = $this->parseInt($this->getColumnValue($row, 'Jumlah'));
                    $hargaAwal = $this->parseDecimal($this->getColumnValue($row, 'Harga Awal'));
                    $hargaSetelahDiskon = $this->parseDecimal($this->getColumnValue($row, 'Harga Setelah Diskon'));
                    $totalHargaProduk = $this->parseDecimal($this->getColumnValue($row, 'Total Harga Produk'));
                    
                    $items[] = [
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
                
                // Map status Shopee ke format standard
                $statusPesanan = $this->cleanString($this->getColumnValue($firstRow, 'Status Pesanan'));
                $statusOrder = $this->mapShopeeStatus($statusPesanan);
                
                $transactions[] = $this->buildTransaction([
                    'order_id' => $this->cleanString($orderId),
                    'tanggal_order' => $this->parseDate($this->getColumnValue($firstRow, 'Waktu Pesanan Selesai')) ?: 
                                       $this->parseDate($this->getColumnValue($firstRow, 'Waktu Pesanan Dibuat')),
                    'status_order' => $statusOrder,
                    'total_pesanan' => $totalPesanan,
                    'total_diskon' => $totalDiskon,
                    'ongkos_kirim' => $ongkosKirim,
                    'biaya_komisi' => $biayaKomisi,
                    'pendapatan_bersih' => $pendapatanBersih,
                    'nama_customer' => $this->cleanString($this->getColumnValue($firstRow, 'Nama Penerima')),
                    'kota_customer' => $this->cleanString($this->getColumnValue($firstRow, 'Kota/Kabupaten')),
                    'provinsi_customer' => $this->cleanString($this->getColumnValue($firstRow, 'Provinsi')),
                    'items' => $items,
                ]);
                
            } catch (\Exception $e) {
                $errors[] = "Error parsing order {$orderId}: " . $e->getMessage();
            }
        }
        
        return [
            'transactions' => $transactions,
            'summary' => [
                'total_orders' => count($transactions),
                'total_rows' => $rows->count(),
                'errors' => $errors,
            ]
        ];
    }
    
    /**
     * Get column value by header name
     */
    private function getColumnValue($row, string $columnName)
    {
        $index = array_search($columnName, $this->header);
        
        if ($index === false) {
            return null;
        }
        
        return $row[$index] ?? null;
    }
    
    /**
     * Map Shopee status to standard format
     */
    private function mapShopeeStatus(string $status): string
    {
        $status = strtolower($status);
        
        $mapping = [
            'selesai' => 'selesai',
            'completed' => 'selesai',
            'delivered' => 'selesai',
            'sedang dikirim' => 'proses',
            'shipping' => 'proses',
            'siap dikirim' => 'proses',
            'ready to ship' => 'proses',
            'dikemas' => 'proses',
            'processing' => 'proses',
            'dibatalkan' => 'dibatalkan',
            'cancelled' => 'dibatalkan',
            'batal' => 'dibatalkan',
            'pengembalian/penukaran barang' => 'dikembalikan',
            'returned' => 'dikembalikan',
            'dikembalikan' => 'dikembalikan',
        ];
        
        return $mapping[$status] ?? 'proses';
    }
}