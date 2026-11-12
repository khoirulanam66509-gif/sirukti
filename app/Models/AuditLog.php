<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_log';

    protected $primaryKey = 'id_audit';

    const UPDATED_AT = null;

    protected $fillable = [
        'id_pengguna',
        'aksi',
        'modul',
        'target_label',
        'detail_perubahan',
        'ip_address',
    ];

    protected $casts = [
        'detail_perubahan' => 'array',
        'created_at' => 'datetime',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}
