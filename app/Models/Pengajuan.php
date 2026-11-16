<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use PDOException;

class Pengajuan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Menyimpan state transisi status sebelum UPDATE untuk mendeteksi apakah trigger DB aktif.
     *
     * @var array<int, array{status_lama: ?string, log_count: int}>
     */
    protected static array $pendingStatusTransitions = [];

    protected static function booted(): void
    {
        // Fallback setara trg_pengajuan_bu (BEFORE UPDATE)
        static::updating(function (Pengajuan $pengajuan) {
            $oldStatus = $pengajuan->getOriginal('status');
            $newStatus = $pengajuan->status;

            if (in_array($oldStatus, ['selesai', 'ditolak'], true) && $newStatus !== $oldStatus) {
                throw new QueryException(
                    $pengajuan->getConnectionName() ?? config('database.default'),
                    'UPDATE pengajuan',
                    [],
                    new PDOException("SQLSTATE[45000]: <<Unknown error>>: 1644 Pengajuan berstatus final tidak dapat diubah statusnya", 45000)
                );
            }

            if ($newStatus === 'diproses' && $oldStatus !== 'diproses' && $pengajuan->tgl_diproses === null) {
                $pengajuan->tgl_diproses = now();
            }

            if ($newStatus === 'selesai' && $oldStatus !== 'selesai') {
                $pengajuan->tgl_selesai = now();
            }

            if ($newStatus !== $oldStatus && $pengajuan->id_pengajuan) {
                static::$pendingStatusTransitions[$pengajuan->id_pengajuan] = [
                    'status_lama' => $oldStatus,
                    'log_count' => $pengajuan->log()->count(),
                ];
            }
        });

        // Fallback setara trg_pengajuan_ai (AFTER INSERT) bila trigger DB tidak aktif di cloud MySQL
        static::created(function (Pengajuan $pengajuan) {
            if (! $pengajuan->log()->exists()) {
                StatusLog::create([
                    'id_pengajuan' => $pengajuan->id_pengajuan,
                    'status_lama' => null,
                    'status_baru' => $pengajuan->status ?? 'menunggu',
                    'id_pengguna' => $pengajuan->id_pengubah,
                    'catatan' => 'Pengajuan dibuat',
                ]);
            }
        });

        // Fallback setara trg_pengajuan_au (AFTER UPDATE) bila trigger DB tidak aktif di cloud MySQL
        static::updated(function (Pengajuan $pengajuan) {
            $pending = static::$pendingStatusTransitions[$pengajuan->id_pengajuan] ?? null;
            unset(static::$pendingStatusTransitions[$pengajuan->id_pengajuan]);

            if ($pending && $pengajuan->status !== $pending['status_lama']) {
                if ($pengajuan->log()->count() <= $pending['log_count']) {
                    StatusLog::create([
                        'id_pengajuan' => $pengajuan->id_pengajuan,
                        'status_lama' => $pending['status_lama'],
                        'status_baru' => $pengajuan->status,
                        'id_pengguna' => $pengajuan->id_pengubah,
                        'catatan' => $pengajuan->catatan_petugas,
                    ]);
                }
            }
        });
    }

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
