<?php
// app/Models/Produk.php
// FIXED: harga_dasar not harga_jual

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'id_perusahaan',
        'sku',
        'nama_produk',
        'kategori',
        'harga_dasar', // FIXED: not harga_jual
        'is_aktif',
    ];

    protected $casts = [
        'harga_dasar' => 'decimal:2', // FIXED
        'is_aktif' => 'boolean',
    ];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan', 'id_perusahaan');
    }

    public function penjualanTransaksiDetail()
    {
        return $this->hasMany(PenjualanTransaksiDetail::class, 'id_produk', 'id_produk');
    }
}
