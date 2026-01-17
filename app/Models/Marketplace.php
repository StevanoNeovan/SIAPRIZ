<?php
// app/Models/Marketplace.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Marketplace extends Model
{
    use HasFactory;

    protected $table = 'marketplace';
    protected $primaryKey = 'id_marketplace';
    
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null; // No updated_at in marketplace table

    protected $fillable = [
        'nama_marketplace',
        'kode_marketplace',
        'logo_url',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function penjualanTransaksi()
    {
        return $this->hasMany(PenjualanTransaksi::class, 'id_marketplace', 'id_marketplace');
    }

      // Helper methods
    public static function getByKode(string $kode)
    {
        return self::where('kode_marketplace', $kode)
            ->where('is_aktif', true)
            ->first();
    }
    
    public static function getAllActive()
    {
        return self::where('is_aktif', true)
            ->orderBy('nama_marketplace')
            ->get();
    }

}