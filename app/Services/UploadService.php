<?php
// app/Services/UploadService.php

namespace App\Services;

use App\Models\Marketplace;
use App\Models\Produk;
use App\Models\PenjualanTransaksi;
use App\Models\PenjualanTransaksiDetail;
use App\Models\LogUpload;
use App\Services\Parser\GenericParser;
use App\Services\Parser\ShopeeParser;
use App\Services\Parser\TokopediaParser;
use App\Services\Parser\LazadaParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * Process uploaded file
     */
    public function processUpload($file, int $idPerusahaan, int $idPengguna, int $idMarketplace, bool $useTemplate = true): array
    {
        try {
            // Store file permanently
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $filename, 'public');
            
            // Create upload log
            $logUpload = $this->createUploadLog($file, $idPerusahaan, $idMarketplace, $idPengguna, $filePath);
            
            // Get appropriate parser
            if ($useTemplate) {
                // Use Generic Parser for template SIAPRIZ
                $parser = new GenericParser($file, $idPerusahaan, $idMarketplace);
            } else {
                // Auto-detect and use marketplace-specific parser
                $parser = $this->getMarketplaceParser($file, $idPerusahaan, $idMarketplace);
                
                if (!$parser->validate()) {
                    throw new \Exception('Format file tidak sesuai dengan ' . $parser->getMarketplaceCode());
                }
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
     * Get marketplace-specific parser
     */
    private function getMarketplaceParser($file, int $idPerusahaan, int $idMarketplace)
    {
        $marketplace = Marketplace::find($idMarketplace);
        
        $parsers = [
            'SHOPEE' => ShopeeParser::class,
            'TOKOPEDIA' => TokopediaParser::class,
            'LAZADA' => LazadaParser::class,
        ];
        
        $parserClass = $parsers[$marketplace->kode_marketplace] ?? null;
        
        if (!$parserClass) {
            throw new \Exception('Parser untuk marketplace ini belum tersedia. Gunakan template SIAPRIZ.');
        }
        
        return new $parserClass($file, $idPerusahaan, $idMarketplace);
    }
    
    /**
     * Create upload log entry
     */
    private function createUploadLog($file, int $idPerusahaan, int $idMarketplace, int $idPengguna, string $filePath): LogUpload
    {
        return LogUpload::create([
            'id_perusahaan' => $idPerusahaan,
            'id_marketplace' => $idMarketplace,
            'id_pengguna' => $idPengguna,
            'nama_file' => $file->getClientOriginalName(),
            'file_path' => $filePath,
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
            'status_upload' => 'selesai',
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