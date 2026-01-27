<?php
// app/Services/Parser/Mappers/LazadaColumnMapper.php

namespace App\Services\Parser\Mappers;

use App\Services\Parser\Contracts\ColumnMapperInterface;

/**
 * Column mapper untuk format CSV Lazada asli
 */
class LazadaColumnMapper implements ColumnMapperInterface
{
    public function getRequiredColumns(): array
    {
        return [
            'orderNumber',
            'status',
            'itemName',
            'quantity',
            'unitPrice',
        ];
    }
    
    public function getColumnMapping(): array
    {
        return [
            'order_id' => 'orderNumber',
            'status_order' => 'status',
            'sku' => 'sellerSku',
            'nama_produk' => 'itemName',
            'variasi' => 'variation',
            'quantity' => 'quantity',
            'harga_satuan' => 'unitPrice',
            'total_pesanan' => 'paidPrice',
            'total_diskon' => 'sellerDiscountTotal',
            'ongkos_kirim' => 'shippingFee',
            'biaya_komisi' => 'commission',
            'pendapatan_bersih' => 'pendapatanBersih',
            'nama_customer' => 'billingName',
            'kota_customer' => 'billingAddr4',
            'provinsi_customer' => 'billingAddr3',
        ];
    }
    
    public function getOrderIdColumn(): string
    {
        return 'orderNumber';
    }
    
    public function getDateColumns(): array
    {
        return ['createTime'];
    }
    
    public function getStatusColumn(): string
    {
        return 'status';
    }
}
