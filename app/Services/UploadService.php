<?php
// app/Services/UploadService.php

namespace App\Services;

use App\Models\Marketplace;
use App\Models\Produk;
use App\Models\PenjualanTransaksi;
use App\Models\PenjualanTransaksiDetail;
use App\Models\LogUpload;
use App\Services\Parser\ShopeeParser;
use App\Services\Parser\TokopediaParser;
use App\Services\Parser\LazadaParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UploadService
{
    /**
     * Process uploaded file
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $idPerusahaan
     * @param int $idPengguna
     * @param int|null $idMarketplace (optional - will auto-detect if null)
     * @return array
     */
    public function processUpload($file, int $idPerusahaan, int $idPengguna, ?int $idMarketplace = null): array
    {
        try {
            // Auto-detect marketplace if not provided
            if (!$idMarketplace) {
                $detectedMarketplace = $this->detectMarketplace($file, $idPerusahaan);
                if (!$detectedMarketplace) {
                    throw new \Exception('Format file tidak dikenali. Pastikan file sesuai dengan format Shopee, Tokopedia, atau Lazada.');
                }
                $idMarketplace = $detectedMarketplace->id_marketplace;
            } else {
                $marketplace = Marketplace::find($idMarketplace);
                if (!$marketplace) {
                    throw new \Exception('Marketplace tidak ditemukan.');
                }
            }
            
            // Create upload log
            $logUpload = $this->createUploadLog($file, $idPerusahaan, $idMarketplace, $idPengguna);
            
            // Get appropriate parser
            $parser = $this->getParser($file, $idPerusahaan, $idMarketplace);
            
            if (!$parser->validate()) {
                throw new \Exception('Format file tidak valid untuk marketplace yang dipilih.');
            }
            
            // Parse file
            $parseResult = $parser->parse();
            
            // Process transactions
            $result = $this->saveTransactions(
                $parseResult['transactions'], 
                $logUpload->id_upload
            );
            
            // Update log status
            $this->updateUploadLog($logUpload, $result, $parseResult);
            
            return [
                'success' => true,
                'log_id' => $logUpload->id_upload,
                'total_orders' => $result['success'],
                'total_failed' => $result['failed'],
                'errors' => $result['errors'],
                'summary' => $parseResult['summary'],
            ];
            
        } catch (\Exception $e) {
            Log::error('Upload failed: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'user' => $idPengguna,
            ]);
            
            if (isset($logUpload)) {
                $this->markUploadAsFailed($logUpload, $e->getMessage());
            }
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Auto-detect marketplace from file
     */
    private function detectMarketplace($file, int $idPerusahaan): ?Marketplace
    {
        $parsers = [
            'SHOPEE' => ShopeeParser::class,
            'TOKOPEDIA' => TokopediaParser::class,
            'LAZADA' => LazadaParser::class,
        ];
        
        foreach ($parsers as $code => $parserClass) {
            $marketplace = Marketplace::getByKode($code);
            if (!$marketplace) continue;
            
            $parser = new $parserClass($file, $idPerusahaan, $marketplace->id_marketplace);
            
            if ($parser->validate()) {
                return $marketplace;
            }
        }
        
        return null;
    }
    
    /**
     * Get parser instance based on marketplace
     */
    private function getParser($file, int $idPerusahaan, int $idMarketplace)
    {
        $marketplace = Marketplace::find($idMarketplace);
        
        $parsers = [
            'SHOPEE' => ShopeeParser::class,
            'TOKOPEDIA' => TokopediaParser::class,
            'LAZADA' => LazadaParser::class,
        ];
        
        $parserClass = $parsers[$marketplace->kode_marketplace] ?? null;
        
        if (!$parserClass) {
            throw new \Exception('Parser untuk marketplace ini belum tersedia.');
        }
        
        return new $parserClass($file, $idPerusahaan, $idMarketplace);
    }
    
    /**
     * Create upload log entry
     */
    private function createUploadLog($file, int $idPerusahaan, int $idMarketplace, int $idPengguna): LogUpload
    {
        return LogUpload::create([
            'id_perusahaan' => $idPerusahaan,
            'id_marketplace' => $idMarketplace,
            'id_pengguna' => $idPengguna,
            'nama_file' => $file->getClientOriginalName(),
            'ukuran_file' => $file->getSize(),
            'status_upload' => 'proses',
        ]);
    }
    
    /**
     * Save transactions to database
     */
    private function saveTransactions(array $transactions, int $idBatchUpload): array
    {
        $success = 0;
        $failed = 0;
        $errors = [];
        
        DB::beginTransaction();
        
        try {
            foreach ($transactions as $transaction) {
                try {
                    // Create transaction header
                    $transaksi = PenjualanTransaksi::create(array_merge(
                        $transaction['header'],
                        ['id_batch_upload' => $idBatchUpload]
                    ));
                    
                    // Create transaction details
                    foreach ($transaction['items'] as $item) {
                        // Find or create product
                        $produk = Produk::findOrCreateBySKU(
                            $transaction['header']['id_perusahaan'],
                            $item['sku'],
                            [
                                'nama_produk' => $item['nama_produk'],
                                'harga_dasar' => $item['harga_satuan'],
                            ]
                        );
                        
                        // Create detail
                        PenjualanTransaksiDetail::create([
                            'id_transaksi' => $transaksi->id_transaksi,
                            'id_produk' => $produk->id_produk,
                            'sku' => $item['sku'],
                            'nama_produk' => $item['nama_produk'],
                            'variasi' => $item['variasi'] ?? null,
                            'quantity' => $item['quantity'],
                            'harga_satuan' => $item['harga_satuan'],
                            'subtotal' => $item['subtotal'],
                        ]);
                    }
                    
                    $success++;
                    
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Order {$transaction['header']['order_id']}: " . $e->getMessage();
                    Log::error('Failed to save transaction', [
                        'order_id' => $transaction['header']['order_id'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        
        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }
    
    /**
     * Update upload log after processing
     */
    private function updateUploadLog(LogUpload $logUpload, array $result, array $parseResult): void
    {
        $logUpload->update([
            'total_baris' => $parseResult['summary']['total_rows'] ?? 0,
            'baris_sukses' => $result['success'],
            'baris_gagal' => $result['failed'],
            'status_upload' => $result['failed'] > 0 ? 'selesai' : 'selesai',
            'pesan_error' => !empty($result['errors']) ? implode("\n", array_slice($result['errors'], 0, 5)) : null,
        ]);
    }
    
    /**
     * Mark upload as failed
     */
    private function markUploadAsFailed(LogUpload $logUpload, string $errorMessage): void
    {
        $logUpload->update([
            'status_upload' => 'gagal',
            'pesan_error' => $errorMessage,
        ]);
    }
    
    /**
     * Get upload history
     */
    public function getUploadHistory(int $idPerusahaan, int $limit = 20)
    {
        return LogUpload::byPerusahaan($idPerusahaan)
            ->with(['marketplace', 'pengguna'])
            ->orderBy('tanggal_upload', 'desc')
            ->limit($limit)
            ->get();
    }
}