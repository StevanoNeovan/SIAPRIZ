<?php
// app/Services/Parser/TokopediaParser.php

namespace App\Services\Parser;

class TokopediaParser extends AbstractParser
{
    /**
     * Expected Tokopedia CSV columns (sesuai format asli)
     */
    private const REQUIRED_COLUMNS = [
        'Order ID',
        'Order Status',
        'Product Name',
        'Quantity',
        'Order Amount',
    ];
    
    private $header = [];
    
    public function getMarketplaceCode(): string
    {
        return 'TOKOPEDIA';
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
        
        // Group by Order ID
        $groupedOrders = $rows->groupBy(function($row) {
            return $this->getColumnValue($row, 'Order ID');
        });
        
        foreach ($groupedOrders as $orderId => $orderRows) {
            try {
                $firstRow = $orderRows->first();
                
                // Parse financial data
                $orderAmount = $this->parseDecimal($this->getColumnValue($firstRow, 'Order Amount'));
                $orderRefund = $this->parseDecimal($this->getColumnValue($firstRow, 'Order Refund Amount'));
                
                // Shipping
                $shippingFeeAfterDiscount = $this->parseDecimal($this->getColumnValue($firstRow, 'Shipping Fee After Discount'));
                $originalShippingFee = $this->parseDecimal($this->getColumnValue($firstRow, 'Original Shipping Fee'));
                $shippingFeePlatformDiscount = $this->parseDecimal($this->getColumnValue($firstRow, 'Shipping Fee Platform Discount'));
                $shippingFeeSellerDiscount = $this->parseDecimal($this->getColumnValue($firstRow, 'Shipping Fee Seller Discount'));
                
                // Discount & Fees
                $platformDiscount = $this->parseDecimal($this->getColumnValue($firstRow, 'SKU Platform Discount'));
                $sellerDiscount = $this->parseDecimal($this->getColumnValue($firstRow, 'SKU Seller Discount'));
                $paymentPlatformDiscount = $this->parseDecimal($this->getColumnValue($firstRow, 'Payment platform discount'));
                
                $buyerServiceFee = $this->parseDecimal($this->getColumnValue($firstRow, 'Buyer Service Fee'));
                $handlingFee = $this->parseDecimal($this->getColumnValue($firstRow, 'Handling Fee'));
                $shippingInsurance = $this->parseDecimal($this->getColumnValue($firstRow, 'Shipping Insurance'));
                $itemInsurance = $this->parseDecimal($this->getColumnValue($firstRow, 'Item Insurance'));
                
                // Calculate totals
                $totalDiskon = $platformDiscount + $sellerDiscount + $paymentPlatformDiscount + 
                               $shippingFeePlatformDiscount + $shippingFeeSellerDiscount;
                
                // Biaya komisi = handling fee + buyer service fee (yang jadi beban seller)
                $biayaKomisi = $handlingFee + $buyerServiceFee;
                
                // Total pesanan = order amount (yang dibayar customer)
                $totalPesanan = $orderAmount;
                
                // Ongkir yang dibayar customer
                $ongkosKirim = $shippingFeeAfterDiscount;
                
                // Pendapatan bersih = order amount - biaya komisi - refund
                $pendapatanBersih = $orderAmount - $biayaKomisi - $orderRefund;
                
                // Parse items
                $items = [];
                foreach ($orderRows as $row) {
                    $quantity = $this->parseInt($this->getColumnValue($row, 'Quantity'));
                    $skuUnitPrice = $this->parseDecimal($this->getColumnValue($row, 'SKU Unit Original Price'));
                    $skuSubtotalAfterDiscount = $this->parseDecimal($this->getColumnValue($row, 'SKU Subtotal After Discount'));
                    
                    $items[] = [
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
                
                // Map status Tokopedia
                $orderStatus = $this->cleanString($this->getColumnValue($firstRow, 'Order Status'));
                $statusOrder = $this->mapTokopediaStatus($orderStatus);
                
                // Parse tanggal - prioritas: Delivered > Cancelled > Shipped > Paid > Created
                $tanggalOrder = $this->parseDate($this->getColumnValue($firstRow, 'Delivered Time')) ?:
                               $this->parseDate($this->getColumnValue($firstRow, 'Cancelled Time')) ?:
                               $this->parseDate($this->getColumnValue($firstRow, 'Shipped Time')) ?:
                               $this->parseDate($this->getColumnValue($firstRow, 'Paid Time')) ?:
                               $this->parseDate($this->getColumnValue($firstRow, 'Created Time'));
                
                $transactions[] = $this->buildTransaction([
                    'order_id' => $this->cleanString($orderId),
                    'tanggal_order' => $tanggalOrder,
                    'status_order' => $statusOrder,
                    'total_pesanan' => $totalPesanan,
                    'total_diskon' => $totalDiskon,
                    'ongkos_kirim' => $ongkosKirim,
                    'biaya_komisi' => $biayaKomisi,
                    'pendapatan_bersih' => $pendapatanBersih,
                    'nama_customer' => $this->cleanString($this->getColumnValue($firstRow, 'Recipient')),
                    'kota_customer' => $this->cleanString($this->getColumnValue($firstRow, 'Regency and City')),
                    'provinsi_customer' => $this->cleanString($this->getColumnValue($firstRow, 'Province')),
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
     * Map Tokopedia status to standard format
     */
    private function mapTokopediaStatus(string $status): string
    {
        $status = strtolower($status);
        
        $mapping = [
            'delivered' => 'selesai',
            'finished' => 'selesai',
            'completed' => 'selesai',
            'shipped' => 'proses',
            'processed' => 'proses',
            'on process' => 'proses',
            'ready to ship' => 'proses',
            'awaiting pickup' => 'proses',
            'cancelled' => 'dibatalkan',
            'canceled' => 'dibatalkan',
            'returned' => 'dikembalikan',
            'refunded' => 'dikembalikan',
        ];
        
        return $mapping[$status] ?? 'proses';
    }
}