<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';

    protected $primaryKey = 'id_pengguna';

    protected $fillable = [
        'nama',
        'username',
        'password',
        'no_hp',
        'role',
        'jabatan',
        'nip',
        'file_ttd',
        'file_stempel',
        'wajib_ganti_password',
        'status_aktif',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status_aktif' => 'boolean',
            'wajib_ganti_password' => 'boolean',
        ];
    }

    public function warga(): HasOne
    {
        return $this->hasOne(Warga::class, 'id_pengguna', 'id_pengguna');
    }

    public function pengajuanDiverifikasi(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'id_verifikator', 'id_pengguna');
    }

    public function pengajuanDiubah(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'id_pengubah', 'id_pengguna');
    }

    public function suratDitandatangani(): HasMany
    {
        return $this->hasMany(Surat::class, 'id_penandatangan', 'id_pengguna');
    }

    public function statusLog(): HasMany
    {
        return $this->hasMany(StatusLog::class, 'id_pengguna', 'id_pengguna');
    }

    public function getNamaLengkapAttribute(): ?string
    {
        return $this->nama;
    }
}
