<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturan';

    protected $primaryKey = 'id_pengaturan';

    protected $fillable = [
        'kunci',
        'nilai',
        'id_pengguna',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'array',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Cache in-memory per request agar seluruh pemanggilan Pengaturan::ambil()
     * cukup melakukan 1 query database.
     *
     * @var array<string, mixed>|null
     */
    protected static ?array $cacheMemori = null;

    /**
     * Mengambil nilai pengaturan berdasarkan kunci.
     */
    public static function ambil(string $kunci, $default = null)
    {
        if (static::$cacheMemori === null) {
            static::$cacheMemori = static::all()
                ->pluck('nilai', 'kunci')
                ->all();
        }

        return array_key_exists($kunci, static::$cacheMemori) && static::$cacheMemori[$kunci] !== null
            ? static::$cacheMemori[$kunci]
            : $default;
    }

    /**
     * Menyimpan atau memperbarui pengaturan berdasarkan kunci.
     */
    public static function simpan(string $kunci, array $nilai, ?int $idPengguna = null): static
    {
        $record = static::updateOrCreate(
            ['kunci' => $kunci],
            ['nilai' => $nilai, 'id_pengguna' => $idPengguna]
        );

        if (static::$cacheMemori !== null) {
            static::$cacheMemori[$kunci] = $record->nilai;
        }

        return $record;
    }
}
