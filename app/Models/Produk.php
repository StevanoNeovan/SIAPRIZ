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

     // Helper methods
    public static function findOrCreateBySKU(int $idPerusahaan, string $sku, array $data = [])
    {
        $produk = self::where('id_perusahaan', $idPerusahaan)
            ->where('sku', $sku)
            ->first();
        
        if (!$produk) {
            $produk = self::create([
                'id_perusahaan' => $idPerusahaan,
                'sku' => $sku,
                'nama_produk' => $data['nama_produk'] ?? $sku,
                'kategori' => $data['kategori'] ?? null,
                'harga_dasar' => $data['harga_dasar'] ?? 0,
                'is_aktif' => true,
            ]);
        }
        
        return $produk;
    }
}
