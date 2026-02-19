<?php
// app/Models/LogUpload.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class LogUpload extends Model
{
    use SoftDeletes;
    
    protected $table = 'log_upload';
    protected $primaryKey = 'id_upload';
    public $timestamps = false;
    
    protected $fillable = [
        'id_perusahaan',
        'id_marketplace',
        'id_pengguna',
        'nama_file',
        'file_path',
        'ukuran_file',
        'total_baris',
        'baris_sukses',
        'baris_gagal',
        'status_upload',
        'pesan_error',
        'tanggal_upload',
        'deleted_at',
        'deleted_by',
    ];
    
    protected $casts = [
        'tanggal_upload' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    protected $dates = [
        'tanggal_upload',
        'deleted_at',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->tanggal_upload)) {
                $model->tanggal_upload = now();
            }
        });
    }
    
    /* ==========================================
       RELATIONSHIPS
    ========================================== */
    
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }
    
    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class, 'id_marketplace');
    }
    
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }
    
    public function deletedBy()
    {
        return $this->belongsTo(Pengguna::class, 'deleted_by');
    }
    
    public function transaksi()
    {
        return $this->hasMany(PenjualanTransaksi::class, 'id_batch_upload', 'id_upload');
    }
    
    /* ==========================================
       SCOPES
    ========================================== */
    
    public function scopeByPerusahaan($query, int $idPerusahaan)
    {
        return $query->where('id_perusahaan', $idPerusahaan);
    }
    
    public function scopeSuccessful($query)
    {
        return $query->where('status_upload', 'selesai');
    }
    
    public function scopeFailed($query)
    {
        return $query->where('status_upload', 'gagal');
    }
    
    /* ==========================================
       HELPER METHODS
    ========================================== */
    

       // Helper: Get file URL
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }
        return null;
    }
    /**
     * Check if upload has associated file
     */
    public function hasFile(): bool
    {
        return !empty($this->file_path) && Storage::disk('public')->exists($this->file_path);
    }
    
    /**
     * Get file size in human readable format
     */
    public function getFileSizeFormatted(): string
    {
        $bytes = $this->ukuran_file;
        
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    /**
     * Check if this upload can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Hanya upload yang sudah selesai/gagal yang bisa dihapus
        // Upload yang masih proses tidak boleh dihapus
        return in_array($this->status_upload, ['selesai', 'gagal']);
    }
    
    /**
     * Get transaction count for this upload
     */
    public function getTransactionCount(): int
    {
        return $this->transaksi()->count();
    }
    
    /**
     * Soft delete with cascade
     */
    public function softDeleteWithTransactions(int $deletedBy): bool
    {
        if (!$this->canBeDeleted()) {
            return false;
        }
        
        try {
            \DB::beginTransaction();
            
            // Mark upload as deleted
            $this->deleted_by = $deletedBy;
            $this->deleted_at = now();
            $this->save();
            
            // Soft delete all related transactions
            $this->transaksi()->update(['deleted_at' => now()]);
            
            // Soft delete all transaction details
            $transactionIds = $this->transaksi()->pluck('id_transaksi');
            PenjualanTransaksiDetail::whereIn('id_transaksi', $transactionIds)
                ->update(['deleted_at' => now()]);
            
            \DB::commit();
            
            \Log::info('Upload batch soft deleted', [
                'id_upload' => $this->id_upload,
                'deleted_by' => $deletedBy,
                'transactions_affected' => $transactionIds->count(),
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Failed to soft delete upload batch', [
                'id_upload' => $this->id_upload,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

