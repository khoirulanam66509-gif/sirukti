<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SyaratLayanan extends Model
{
    use HasFactory;

    protected $table = 'syarat_layanan';

    protected $primaryKey = 'id_syarat';

    public $timestamps = false;

    protected $fillable = [
        'id_layanan',
        'nama_syarat',
        'petunjuk',
        'wajib',
        'urutan',
        'status_aktif',
    ];

    protected function casts(): array
    {
        return [
            'wajib' => 'boolean',
            'status_aktif' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'id_layanan', 'id_layanan');
    }

    public function berkas(): HasMany
    {
        return $this->hasMany(BerkasPengajuan::class, 'id_syarat', 'id_syarat');
    }
}
