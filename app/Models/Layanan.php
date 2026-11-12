<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanan';

    protected $primaryKey = 'id_layanan';

    public $timestamps = false;

    protected $fillable = [
        'kode',
        'nama_layanan',
        'deskripsi',
        'status_aktif',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'status_aktif' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function syarat(): HasMany
    {
        return $this->hasMany(SyaratLayanan::class, 'id_layanan', 'id_layanan')->orderBy('urutan');
    }

    public function syaratAktif(): HasMany
    {
        return $this->hasMany(SyaratLayanan::class, 'id_layanan', 'id_layanan')
            ->where('status_aktif', true)
            ->orderBy('urutan');
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'id_layanan', 'id_layanan');
    }

    public function counterSurat(): HasMany
    {
        return $this->hasMany(CounterSurat::class, 'id_layanan', 'id_layanan');
    }
}
