<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';
    protected $primaryKey = 'id_role';
    
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = ['nama_role'];

    public function pengguna()
    {
        return $this->hasMany(Pengguna::class, 'id_role', 'id_role');
    }
}