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
     * Mengambil nilai pengaturan berdasarkan kunci.
     */
    public static function ambil(string $kunci, $default = null)
    {
        $setting = static::where('kunci', $kunci)->first();

        return $setting ? $setting->nilai : $default;
    }

    /**
     * Menyimpan atau memperbarui pengaturan berdasarkan kunci.
     */
    public static function simpan(string $kunci, array $nilai, ?int $idPengguna = null): static
    {
        return static::updateOrCreate(
            ['kunci' => $kunci],
            ['nilai' => $nilai, 'id_pengguna' => $idPengguna]
        );
    }
}
