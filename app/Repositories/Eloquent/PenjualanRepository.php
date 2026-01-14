<?php
// app/Repositories/Eloquent/PenjualanRepository.php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\PenjualanRepositoryInterface;
use App\Models\PenjualanTransaksi;
use App\Models\PenjualanTransaksiDetail;
use App\Models\LogUpload;
use Illuminate\Support\Facades\DB;

class PenjualanRepository implements PenjualanRepositoryInterface
{
    /**
     * Create new transaction
     */
    public function createTransaction(array $data): int
    {
        $transaksi = PenjualanTransaksi::create($data);
        return $transaksi->id_transaksi;
    }
    
    /**
     * Create transaction details (bulk insert)
     */
    public function createTransactionDetails(int $idTransaksi, array $items): void
    {
        $details = array_map(function($item) use ($idTransaksi) {
            return array_merge($item, ['id_transaksi' => $idTransaksi]);
        }, $items);
        
        PenjualanTransaksiDetail::insert($details);
    }
    
    /**
     * Process upload via stored procedure
     */
    public function processUpload(int $idUpload): bool
    {
        try {
            DB::select('CALL sp_proses_upload(?)', [$idUpload]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Create upload log
     */
    public function logUpload(array $logData): int
    {
        $log = LogUpload::create($logData);
        return $log->id_upload;
    }
    
    /**
     * Update upload status
     */
    public function updateUploadStatus(int $idUpload, string $status, array $stats): void
    {
        LogUpload::where('id_upload', $idUpload)->update([
            'status_upload' => $status,
            'baris_sukses' => $stats['sukses'] ?? 0,
            'baris_gagal' => $stats['gagal'] ?? 0,
            'pesan_error' => $stats['error'] ?? null,
        ]);
    }
}