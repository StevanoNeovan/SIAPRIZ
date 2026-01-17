<?php
// app/Models/LogUpload.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogUpload extends Model
{
    protected $table = 'log_upload';
    protected $primaryKey = 'id_upload';
    
    const CREATED_AT = 'tanggal_upload';
    const UPDATED_AT = null;
    
    protected $fillable = [
        'id_perusahaan',
        'id_marketplace',
        'id_pengguna',
        'nama_file',
        'ukuran_file',
        'total_baris',
        'baris_sukses',
        'baris_gagal',
        'status_upload',
        'pesan_error',
    ];
    
    protected $casts = [
        'ukuran_file' => 'integer',
        'total_baris' => 'integer',
        'baris_sukses' => 'integer',
        'baris_gagal' => 'integer',
    ];
    
    // Relationships
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan', 'id_perusahaan');
    }
    
    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class, 'id_marketplace', 'id_marketplace');
    }
    
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
    
    public function transaksi()
    {
        return $this->hasMany(PenjualanTransaksi::class, 'id_batch_upload', 'id_upload');
    }
    
    // Scopes
    public function scopeByPerusahaan($query, int $idPerusahaan)
    {
        return $query->where('id_perusahaan', $idPerusahaan);
    }
    
    public function scopeSelesai($query)
    {
        return $query->where('status_upload', 'selesai');
    }
    
    public function scopeGagal($query)
    {
        return $query->where('status_upload', 'gagal');
    }
}