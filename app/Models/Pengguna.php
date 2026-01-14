<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'id_perusahaan',
        'id_role',
        'username',
        'email',
        'password',
        'nama_lengkap',
        'is_aktif',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'login_terakhir' => 'datetime',
    ];

    // Relationships
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan', 'id_perusahaan');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    // Helper methods
    public function isAdministrator(): bool
    {
        return $this->role->nama_role === 'Administrator';
    }

    public function isCEO(): bool
    {
        return $this->role->nama_role === 'CEO';
    }

    // Override getAuthPassword for custom password field
    public function getAuthPassword()
    {
        return $this->password;
    }
}