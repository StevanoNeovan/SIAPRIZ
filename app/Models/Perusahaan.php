<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $table = 'perusahaan';
    protected $primaryKey = 'id_perusahaan';
    
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nama_perusahaan',
        'logo_url',
        'bidang_usaha',
        'jenis_usaha',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function pengguna()
    {
        return $this->hasMany(Pengguna::class, 'id_perusahaan', 'id_perusahaan');
    }
}