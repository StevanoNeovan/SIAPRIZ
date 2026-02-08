<?php
// app/Services/Parser/Mappers/TokopediaColumnMapper.php

namespace App\Services\Parser\Mappers;

use App\Services\Parser\Contracts\ColumnMapperInterface;

/**
 * Column mapper untuk format CSV Tokopedia asli
 * UPDATED: Hapus biaya_komisi dan pendapatan_bersih
 */
class TokopediaColumnMapper implements ColumnMapperInterface
{
    public function getRequiredColumns(): array
    {
        return [
            'Order ID',
            'Order Status',
            'Product Name',
            'Quantity',
            'Order Amount',
        ];
    }
    
    public function getColumnMapping(): array
    {
        return [
            'order_id' => 'Order ID',
            'status_order' => 'Order Status',
            'sku' => 'Seller SKU',
            'nama_produk' => 'Product Name',
            'variasi' => 'Variation',
            'quantity' => 'Quantity',
            'harga_satuan' => 'SKU Unit Original Price',
            'total_pesanan' => 'Order Amount',
            'total_diskon' => 'SKU Platform Discount',
            'ongkos_kirim' => 'Shipping Fee After Discount',
            'nama_customer' => 'Recipient',
            'kota_customer' => 'Regency and City',
            'provinsi_customer' => 'Province',
        ];
    }
    
    public function getOrderIdColumn(): string
    {
        return 'Order ID';
    }
    
    public function getDateColumns(): array
    {
        return [
            'Delivered Time',
            'Cancelled Time',
            'Shipped Time',
            'Paid Time',
            'Created Time',
        ];
    }
    
    public function getStatusColumn(): string
    {
        return 'Order Status';
    }
}