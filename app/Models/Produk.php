<?php
// app/Models/Produk.php

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
        'harga_jual',
        'is_aktif',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
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