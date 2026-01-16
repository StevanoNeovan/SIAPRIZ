<?php


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
        'nomor_order',
        'tanggal_order',
        'status_order',
        'total_harga',
        'ongkos_kirim',
        'biaya_admin',
        'diskon',
        'pendapatan_bersih',
    ];

    protected $casts = [
        'tanggal_order' => 'datetime',
        'total_harga' => 'decimal:2',
        'ongkos_kirim' => 'decimal:2',
        'biaya_admin' => 'decimal:2',
        'diskon' => 'decimal:2',
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
}