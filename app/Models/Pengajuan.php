<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengajuan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengajuan';

    protected $primaryKey = 'id_pengajuan';

    protected $fillable = [
        'no_pengajuan',
        'id_warga',
        'id_layanan',
        'tgl_pengajuan',
        'status',
        'data_isian',
        'catatan_petugas',
        'id_verifikator',
        'id_pengubah',
        'tgl_diproses',
        'tgl_selesai',
    ];

    protected function casts(): array
    {
        return [
            'data_isian' => 'array',
            'tgl_pengajuan' => 'datetime',
            'tgl_diproses' => 'datetime',
            'tgl_selesai' => 'datetime',
        ];
    }

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'id_warga', 'id_warga');
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'id_layanan', 'id_layanan');
    }

    public function berkas(): HasMany
    {
        return $this->hasMany(BerkasPengajuan::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function log(): HasMany
    {
        return $this->hasMany(StatusLog::class, 'id_pengajuan', 'id_pengajuan')->orderBy('id_log');
    }

    public function statusLogs(): HasMany
    {
        return $this->log();
    }

    public function surat(): HasOne
    {
        return $this->hasOne(Surat::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_verifikator', 'id_pengguna');
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengubah', 'id_pengguna');
    }
}
