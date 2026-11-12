<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BerkasPengajuan extends Model
{
    use HasFactory;

    protected $table = 'berkas_pengajuan';

    protected $primaryKey = 'id_berkas';

    protected $fillable = [
        'id_pengajuan',
        'id_syarat',
        'nama_file_asli',
        'path_file',
        'mime',
        'ukuran_kb',
        'status_verifikasi',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'ukuran_kb' => 'integer',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function syarat(): BelongsTo
    {
        return $this->belongsTo(SyaratLayanan::class, 'id_syarat', 'id_syarat');
    }
}
