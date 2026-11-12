<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusLog extends Model
{
    use HasFactory;

    protected $table = 'status_log';

    protected $primaryKey = 'id_log';

    const UPDATED_AT = null;

    protected $fillable = [
        'id_pengajuan',
        'status_lama',
        'status_baru',
        'id_pengguna',
        'catatan',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}
