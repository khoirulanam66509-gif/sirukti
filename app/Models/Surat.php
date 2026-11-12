<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'surat';

    protected $primaryKey = 'id_surat';

    protected $fillable = [
        'id_pengajuan',
        'no_surat',
        'tgl_surat',
        'kode_verifikasi',
        'file_pdf',
        'id_penandatangan',
        'ditandatangani_at',
    ];

    protected function casts(): array
    {
        return [
            'tgl_surat' => 'date',
            'ditandatangani_at' => 'datetime',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function penandatangan(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_penandatangan', 'id_pengguna');
    }
}
