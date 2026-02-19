<?php
// app/Models/PenjualanTransaksiDetail.php
// FIXED: Column names sesuai schema

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenjualanTransaksiDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'penjualan_transaksi_detail';
    protected $primaryKey = 'id_detail';
    
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null; // No updated_at in schema

    protected $fillable = [
        'id_transaksi',
        'id_produk',
        'sku',
        'nama_produk',
        'variasi',
        'quantity', // FIXED: not jumlah
        'harga_satuan',
        'subtotal',
        'deleted_at',
    ];

    protected $casts = [
        'quantity' => 'integer', // FIXED
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'deleted_at',
    ];

    public function transaksi()
    {
        return $this->belongsTo(PenjualanTransaksi::class, 'id_transaksi', 'id_transaksi');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}