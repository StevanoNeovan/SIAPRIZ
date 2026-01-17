<?php
// app/Services/Parser/LazadaParser.php

namespace App\Services\Parser;

class LazadaParser extends AbstractParser
{
    /**
     * Required Lazada CSV columns (FORMAT ASLI LAZADA)
     * Kolom minimal agar 1 transaksi valid
     */
    private const REQUIRED_COLUMNS = [
        'orderNumber',
        'status',
        'itemName',
        'quantity',
        'unitPrice',
    ];

    private array $header = [];

    public function getMarketplaceCode(): string
    {
        return 'LAZADA';
    }

    public function validate(): bool
    {
        $data = $this->readFile();

        if ($data->isEmpty()) {
            return false;
        }

        // Simpan header
        $this->header = $data->first()->toArray();

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

        // Simpan header
        $this->header = $data->first()->toArray();

        // Skip header
        $rows = $data->skip(1);

        // Group by orderNumber
        $groupedOrders = $rows->groupBy(fn ($row) =>
            $this->getColumnValue($row, 'orderNumber')
        );

        foreach ($groupedOrders as $orderNumber => $orderRows) {
            try {
                $firstRow = $orderRows->first();

                // ===== Financial =====
                $totalPesanan = $this->parseDecimal(
                    $this->getColumnValue($firstRow, 'paidPrice')
                );

                $ongkosKirim = $this->parseDecimal(
                    $this->getColumnValue($firstRow, 'shippingFee')
                );

                $biayaKomisi = $this->parseDecimal(
                    $this->getColumnValue($firstRow, 'commission')
                );

                $voucher = $this->parseDecimal(
                    $this->getColumnValue($firstRow, 'sellerDiscountTotal')
                );

                $pendapatanBersih = $totalPesanan - $biayaKomisi - $voucher;

                // ===== Items =====
                $items = [];

                foreach ($orderRows as $row) {
                    $quantity = $this->parseInt(
                        $this->getColumnValue($row, 'quantity')
                    );

                    $hargaSatuan = $this->parseDecimal(
                        $this->getColumnValue($row, 'unitPrice')
                    );

                    $items[] = [
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

                // ===== Transaction =====
                $transactions[] = $this->buildTransaction([
                    'order_id' => $this->cleanString($orderNumber),

                    'tanggal_order' => $this->parseDate(
                        $this->getColumnValue($firstRow, 'createTime')
                    ),

                    'status_order' => $this->mapStatus(
                        $this->getColumnValue($firstRow, 'status')
                    ),

                    'total_pesanan' => $totalPesanan,
                    'total_diskon' => $voucher,
                    'ongkos_kirim' => $ongkosKirim,
                    'biaya_komisi' => $biayaKomisi,
                    'pendapatan_bersih' => $pendapatanBersih,

                    'nama_customer' => $this->cleanString(
                        $this->getColumnValue($firstRow, 'customerName')
                    ),

                    'items' => $items,
                ]);

            } catch (\Exception $e) {
                $errors[] = "Error parsing order {$orderNumber}: {$e->getMessage()}";
            }
        }

        return [
            'transactions' => $transactions,
            'summary' => [
                'total_orders' => count($transactions),
                'total_rows' => $rows->count(),
                'errors' => $errors,
            ],
        ];
    }

    /**
     * Get column value by header name
     */
    private function getColumnValue($row, string $columnName)
    {
        $index = array_search($columnName, $this->header, true);

        if ($index === false) {
            return null;
        }

        return $row[$index] ?? null;
    }
}
