<?php
// app/Models/PenjualanTransaksi.php
// FIXED: Column names sesuai schema database

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PenjualanTransaksi extends Model
{
    use HasFactory;

    protected $table = 'penjualan_transaksi';
    protected $primaryKey = 'id_transaksi';
    
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'id_perusahaan',
        'id_marketplace',
        'order_id', // FIXED: not nomor_order
        'tanggal_order',
        'status_order',
        'total_pesanan', // FIXED: not total_harga
        'total_diskon', // FIXED: not diskon
        'ongkos_kirim',
        'biaya_komisi', // FIXED: not biaya_admin
        'pendapatan_bersih',
        'nama_customer',
        'kota_customer',
        'provinsi_customer',
        'id_batch_upload',
    ];

    protected $casts = [
        'tanggal_order' => 'date',
        'total_pesanan' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'ongkos_kirim' => 'decimal:2',
        'biaya_komisi' => 'decimal:2',
        'pendapatan_bersih' => 'decimal:2',
    ];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan', 'id_perusahaan');
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class, 'id_marketplace', 'id_marketplace');
    }

    public function details()
    {
        return $this->hasMany(PenjualanTransaksiDetail::class, 'id_transaksi', 'id_transaksi');
    }

    public function logUpload()
    {
        return $this->belongsTo(LogUpload::class, 'id_batch_upload', 'id_upload');
    }

     // Scopes
    public function scopeSelesai($query)
    {
        return $query->where('status_order', 'selesai');
    }
    
    public function scopeByPerusahaan($query, int $idPerusahaan)
    {
        return $query->where('id_perusahaan', $idPerusahaan);
    }
    
    public function scopeByMarketplace($query, int $idMarketplace)
    {
        return $query->where('id_marketplace', $idMarketplace);
    }
    
    public function scopePeriode($query, string $tanggalMulai, string $tanggalAkhir)
    {
        return $query->whereBetween('tanggal_order', [$tanggalMulai, $tanggalAkhir]);
    }

}
