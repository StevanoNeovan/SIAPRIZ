<?php
// app/Services/Parser/AbstractParser.php

namespace App\Services\Parser;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Parser\Contracts\ColumnMapperInterface;
use App\Services\Parser\Contracts\StatusMapperInterface;
use App\Services\Parser\Validators\OrderValidator;
use App\Services\Parser\Exceptions\ParserException;

abstract class AbstractParser
{
    protected $file;
    protected $idPerusahaan;
    protected $idMarketplace;
    protected $header = [];
    
    public function __construct($file, int $idPerusahaan, int $idMarketplace)
    {
        $this->file = $file;
        $this->idPerusahaan = $idPerusahaan;
        $this->idMarketplace = $idMarketplace;
    }
    
    /**
     * Get column mapper untuk parser ini
     * 
     * @return ColumnMapperInterface
     */
    abstract protected function getColumnMapper(): ColumnMapperInterface;
    
    /**
     * Get status mapper untuk parser ini
     * 
     * @return StatusMapperInterface
     */
    abstract protected function getStatusMapper(): StatusMapperInterface;
    
    /**
     * Get marketplace code
     * 
     * @return string
     */
    abstract public function getMarketplaceCode(): string;
    
    /**
     * Parse file and return structured data
     * 
     * @return array ['transactions' => [], 'summary' => []]
     */
    public function parse(): array
    {
        $data = $this->readFile();
        $transactions = [];
        $skipped = [];
        $errors = [];
        
        // Store header
        $this->header = $data->first()->toArray();
        
        // Skip header row
        $rows = $data->skip(1);
        
        $columnMapper = $this->getColumnMapper();
        $statusMapper = $this->getStatusMapper();
        
        // Group by order ID
        $orderIdColumn = $columnMapper->getOrderIdColumn();
        $groupedOrders = $rows->groupBy(function($row) use ($orderIdColumn) {
            return $this->getColumnValue($row, $orderIdColumn);
        });
        
        foreach ($groupedOrders as $orderId => $orderRows) {
            try {
                // Clean order ID (remove tabs, extra spaces, etc)
                $cleanOrderId = $this->cleanString($orderId);
                
                $firstRowRaw = $orderRows->first();
                $firstRow = $firstRowRaw instanceof Collection ? $firstRowRaw->toArray() : $firstRowRaw;
                
                // Parse financial data untuk validation
                $financialData = $this->parseOrderFinancialData($orderRows, $columnMapper);
                
                // Parse status
                $statusColumn = $columnMapper->getStatusColumn();
                $rawStatus = $this->cleanString($this->getColumnValue($firstRow, $statusColumn));
                $statusOrder = $statusMapper->mapStatus($rawStatus);
                
                // Log untuk debugging
                \Illuminate\Support\Facades\Log::debug('Order validation', [
                    'order_id' => $cleanOrderId,
                    'raw_status' => $rawStatus,
                    'mapped_status' => $statusOrder,
                    'total_pesanan' => $financialData['total_pesanan'],
                    'rows' => $orderRows->count(),
                ]);

                
                // Validate order sebelum diproses
                OrderValidator::validate($cleanOrderId, [
                    'order_id' => $cleanOrderId,
                    'status_order' => $statusOrder,
                    'total_pesanan' => $financialData['total_pesanan'],
                ]);
                
                // Parse order jika valid
                $transaction = $this->parseOrder($orderId, $orderRows, $columnMapper, $statusMapper);
                if ($transaction) {
                    $transactions[] = $transaction;
                }
            } catch (ParserException $e) {
                // Handle parser exceptions dengan kategori
                if ($e->getErrorType() === 'skipped') {
                    $skipped[] = $e->getUserMessage();
                    \Illuminate\Support\Facades\Log::info('Order skipped', [
                        'order_id' => $orderId,
                        'reason' => $e->getMessage(),
                    ]);
                } else {
                    $errors[] = $e->getUserMessage();
                    \Illuminate\Support\Facades\Log::warning('Order validation error', [
                        'order_id' => $orderId,
                        'error' => $e->getMessage(),
                    ]);
                }
            } catch (\Exception $e) {
                // Handle unexpected exceptions dengan generic message
                $errors[] = "Pesanan {$orderId} gagal diproses. Silakan hubungi administrator.";
                
                // Log actual error untuk debugging
                \Illuminate\Support\Facades\Log::error('Parser error', [
                    'order_id' => $orderId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
        
        return [
            'transactions' => $transactions,
            'summary' => [
                'total_orders' => count($transactions),
                'total_rows' => $rows->count(),
                'skipped' => $skipped,
                'errors' => $errors,
            ]
        ];
    }
    
    /**
     * Validate if file format matches this parser
     * 
     * @return bool
     */
    public function validate(): bool
    {
        $data = $this->readFile();
        
        if ($data->isEmpty()) {
            return false;
        }
        
        // Store header for later use
        $this->header = $data->first()->toArray();
        
        // Check if header contains required columns
        $columnMapper = $this->getColumnMapper();
        $requiredColumns = $columnMapper->getRequiredColumns();
        
        // Check if at least 80% of required columns exist
        $foundCount = 0;
        foreach ($requiredColumns as $column) {
            if (in_array($column, $this->header)) {
                $foundCount++;
            }
        }
        
        // Require at least 80% of columns to be present
        $requiredPercentage = 0.8;
        $minimumRequired = ceil(count($requiredColumns) * $requiredPercentage);
        
        return $foundCount >= $minimumRequired;
    }
    
    /**
     * Parse single order dengan multiple items
     * 
     * @param string $orderId
     * @param Collection $orderRows
     * @param ColumnMapperInterface $columnMapper
     * @param StatusMapperInterface $statusMapper
     * @return array|null
     */
    protected function parseOrder(
        string $orderId, 
        Collection $orderRows, 
        ColumnMapperInterface $columnMapper,
        StatusMapperInterface $statusMapper
    ): ?array
    {
        $firstRowRaw = $orderRows->first();
        // Convert Collection to array if needed
        $firstRow = $firstRowRaw instanceof Collection ? $firstRowRaw->toArray() : $firstRowRaw;
        
        // Parse items
        $items = $this->parseItems($orderRows, $columnMapper);
        
        // Parse financial data - UPDATED: hanya ambil total_pesanan
        $financialData = $this->parseOrderFinancialData($orderRows, $columnMapper);
        
        // Parse dates
        $tanggalOrder = $this->parseDateFromRow($firstRow, $columnMapper);
        
        // Parse status
        $statusColumn = $columnMapper->getStatusColumn();
        $statusOrder = $statusMapper->mapStatus(
            $this->cleanString($this->getColumnValue($firstRow, $statusColumn))
        );
        
        // Parse customer info
        $customerData = $this->parseCustomerData($firstRow, $columnMapper);
        
        return $this->buildTransaction([
            'order_id' => $this->cleanString($orderId),
            'tanggal_order' => $tanggalOrder,
            'status_order' => $statusOrder,
            'total_pesanan' => $financialData['total_pesanan'],
            'total_diskon' => $financialData['total_diskon'] ?? 0,
            'ongkos_kirim' => $financialData['ongkos_kirim'] ?? 0,
            'biaya_komisi' => 0, // Set to 0 - diabaikan
            'pendapatan_bersih' => $financialData['total_pesanan'], // Sama dengan total_pesanan
            'nama_customer' => $customerData['nama_customer'],
            'kota_customer' => $customerData['kota_customer'],
            'provinsi_customer' => $customerData['provinsi_customer'],
            'items' => $items,
        ]);
    }
    
    /**
     * Parse items dari order rows
     * Override di subclass jika perlu custom logic
     * 
     * @param Collection $orderRows
     * @param ColumnMapperInterface $columnMapper
     * @return array
     */
    protected function parseItems(Collection $orderRows, ColumnMapperInterface $columnMapper): array
    {
        $items = [];
        
        foreach ($orderRows as $row) {
            // Convert Collection to array if needed
            $rowArray = $row instanceof Collection ? $row->toArray() : $row;
            $item = $this->parseItem($rowArray, $columnMapper);
            if ($item) {
                $items[] = $item;
            }
        }
        
        return $items;
    }
    
    /**
     * Parse single item
     * Override di subclass untuk custom logic
     * 
     * @param array $row
     * @param ColumnMapperInterface $columnMapper
     * @return array|null
     */
    protected function parseItem(array $row, ColumnMapperInterface $columnMapper): ?array
    {
        $mapping = $columnMapper->getColumnMapping();
        
        // Default item parsing - override di subclass jika perlu
        return [
            'sku' => $this->cleanString($this->getColumnValue($row, $mapping['sku'] ?? 'SKU')) ?: 'SKU-' . uniqid(),
            'nama_produk' => $this->cleanString($this->getColumnValue($row, $mapping['nama_produk'] ?? 'Nama Produk')),
            'variasi' => $this->cleanString($this->getColumnValue($row, $mapping['variasi'] ?? 'Variasi')),
            'quantity' => $this->parseInt($this->getColumnValue($row, $mapping['quantity'] ?? 'Jumlah')),
            'harga_satuan' => $this->parseDecimal($this->getColumnValue($row, $mapping['harga_satuan'] ?? 'Harga Satuan')),
            'subtotal' => $this->parseDecimal($this->getColumnValue($row, $mapping['subtotal'] ?? 'Subtotal')),
        ];
    }
    
    /**
     * Parse financial data dari row
     * UPDATED: Sederhanakan - hanya ambil total_pesanan, diskon, ongkir
     * Abaikan biaya_komisi dan pendapatan_bersih
     * 
     * @param array $row
     * @param ColumnMapperInterface $columnMapper
     * @return array
     */
    protected function parseFinancialData(array $row, ColumnMapperInterface $columnMapper): array
    {
        $mapping = $columnMapper->getColumnMapping();
        
        $totalPesanan = $this->parseDecimal($this->getColumnValue($row, $mapping['total_pesanan'] ?? 'Total Pesanan'));
        
        return [
            'total_pesanan' => $totalPesanan,
            'total_diskon' => $this->parseDecimal($this->getColumnValue($row, $mapping['total_diskon'] ?? 'Total Diskon')),
            'ongkos_kirim' => $this->parseDecimal($this->getColumnValue($row, $mapping['ongkos_kirim'] ?? 'Ongkos Kirim')),
            // biaya_komisi dan pendapatan_bersih diabaikan
        ];
    }

    /**
     * Parse financial data level order
     * Default: ambil dari row pertama
     * UPDATED: hanya ambil total_pesanan
     */
    protected function parseOrderFinancialData(\Illuminate\Support\Collection $orderRows, ColumnMapperInterface $columnMapper): array 
    {
        $firstRowRaw = $orderRows->first();
        $firstRow = $firstRowRaw instanceof \Illuminate\Support\Collection
            ? $firstRowRaw->toArray()
            : $firstRowRaw;

        return $this->parseFinancialData($firstRow, $columnMapper);
    }

    
    
    /**
     * Parse customer data dari row
     * Override di subclass untuk custom logic
     * 
     * @param array $row
     * @param ColumnMapperInterface $columnMapper
     * @return array
     */
    protected function parseCustomerData(array $row, ColumnMapperInterface $columnMapper): array
    {
        $mapping = $columnMapper->getColumnMapping();
        
        return [
            'nama_customer' => $this->cleanString($this->getColumnValue($row, $mapping['nama_customer'] ?? 'Nama Customer')),
            'kota_customer' => $this->cleanString($this->getColumnValue($row, $mapping['kota_customer'] ?? 'Kota')),
            'provinsi_customer' => $this->cleanString($this->getColumnValue($row, $mapping['provinsi_customer'] ?? 'Provinsi')),
        ];
    }
    
    /**
     * Parse date dari row dengan multiple date columns
     * 
     * @param array $row
     * @param ColumnMapperInterface $columnMapper
     * @return string
     */
    protected function parseDateFromRow(array $row, ColumnMapperInterface $columnMapper): string
    {
        $dateColumns = $columnMapper->getDateColumns();
        
        foreach ($dateColumns as $column) {
            $value = $this->getColumnValue($row, $column);
            if (!is_null($value) && $value !== '') {
                $parsed = $this->parseDate($value);
                if ($parsed !== now()->format('Y-m-d')) {
                    return $parsed;
                }
            }
        }
        
        return now()->format('Y-m-d');
    }
    
    /**
     * Read CSV/Excel file to collection
     * 
     * @return Collection
     */
    protected function readFile(): Collection
    {
        $data = Excel::toCollection(null, $this->file)->first();
        
        // Remove empty rows
        return $data->filter(function($row) {
            return $row->filter()->isNotEmpty();
        });
    }
    
    /**
     * Get column value by header name
     * 
     * @param array $row
     * @param string $columnName
     * @return mixed
     */
    protected function getColumnValue($row, string $columnName)
    {
        $index = array_search($columnName, $this->header);
        
        if ($index === false) {
            return null;
        }
        
        return $row[$index] ?? null;
    }
    
    /**
     * Clean and normalize string
     * 
     * @param mixed $value
     * @return string
     */
    protected function cleanString($value): string
    {
        if (is_null($value)) {
            return '';
        }
        
        return trim((string) $value);
    }
    
    /**
     * Parse decimal/currency value
     * 
     * @param mixed $value
     * @return float
     */
    protected function parseDecimal($value): float
    {
        if (is_null($value) || $value === '') {
            return 0;
        }
        
        // Remove currency symbols and thousands separator
        $cleaned = preg_replace('/[^\d,.-]/', '', (string) $value);
        
        // Handle different decimal separators
        $cleaned = str_replace(',', '.', $cleaned);
        
        return (float) $cleaned;
    }
    
    /**
     * Parse integer value
     * 
     * @param mixed $value
     * @return int
     */
    protected function parseInt($value): int
    {
        if (is_null($value) || $value === '') {
            return 0;
        }
        
        return (int) preg_replace('/\D/', '', (string) $value);
    }
    
    /**
     * Parse date to Y-m-d format
     * 
     * @param mixed $value
     * @return string
     */
    protected function parseDate($value): string
    {
        if (is_null($value) || $value === '') {
            return now()->format('Y-m-d');
        }
        
        try {
            // Try multiple date formats
            $date = \Carbon\Carbon::parse($value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }
    
    /**
     * Generate unique transaction structure
     * UPDATED: biaya_komisi = 0, pendapatan_bersih = total_pesanan
     * 
     * @param array $data
     * @return array
     */
    protected function buildTransaction(array $data): array
    {
        return [
            'header' => [
                'id_perusahaan' => $this->idPerusahaan,
                'id_marketplace' => $this->idMarketplace,
                'order_id' => $data['order_id'],
                'tanggal_order' => $data['tanggal_order'],
                'status_order' => $data['status_order'],
                'total_pesanan' => $data['total_pesanan'],
                'total_diskon' => $data['total_diskon'] ?? 0,
                'ongkos_kirim' => $data['ongkos_kirim'] ?? 0,
                'biaya_komisi' => 0, // Diabaikan
                'pendapatan_bersih' => $data['total_pesanan'], // Sama dengan total_pesanan
                'nama_customer' => $data['nama_customer'] ?? null,
                'kota_customer' => $data['kota_customer'] ?? null,
                'provinsi_customer' => $data['provinsi_customer'] ?? null,
            ],
            'items' => $data['items'] ?? []
        ];
    }
}