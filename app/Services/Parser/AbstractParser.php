<?php
// app/Services/Parser/AbstractParser.php

namespace App\Services\Parser;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

abstract class AbstractParser
{
    protected $file;
    protected $idPerusahaan;
    protected $idMarketplace;
    
    public function __construct($file, int $idPerusahaan, int $idMarketplace)
    {
        $this->file = $file;
        $this->idPerusahaan = $idPerusahaan;
        $this->idMarketplace = $idMarketplace;
    }
    
    /**
     * Parse file and return structured data
     * 
     * @return array ['transactions' => [], 'summary' => []]
     */
    abstract public function parse(): array;
    
    /**
     * Validate if file format matches this parser
     * 
     * @return bool
     */
    abstract public function validate(): bool;
    
    /**
     * Get marketplace code
     * 
     * @return string
     */
    abstract public function getMarketplaceCode(): string;
    
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
     * Map status order to standard format
     * 
     * @param string $status
     * @return string
     */
    protected function mapStatus(string $status): string
    {
        $status = strtolower($status);
        
        $mapping = [
            'completed' => 'selesai',
            'delivered' => 'selesai',
            'selesai' => 'selesai',
            'success' => 'selesai',
            'pending' => 'proses',
            'processing' => 'proses',
            'proses' => 'proses',
            'cancelled' => 'dibatalkan',
            'canceled' => 'dibatalkan',
            'dibatalkan' => 'dibatalkan',
            'refunded' => 'dikembalikan',
            'returned' => 'dikembalikan',
            'dikembalikan' => 'dikembalikan',
        ];
        
        return $mapping[$status] ?? 'proses';
    }
    
    /**
     * Generate unique transaction structure
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
                'biaya_komisi' => $data['biaya_komisi'] ?? 0,
                'pendapatan_bersih' => $data['pendapatan_bersih'],
                'nama_customer' => $data['nama_customer'] ?? null,
                'kota_customer' => $data['kota_customer'] ?? null,
                'provinsi_customer' => $data['provinsi_customer'] ?? null,
            ],
            'items' => $data['items'] ?? []
        ];
    }
}