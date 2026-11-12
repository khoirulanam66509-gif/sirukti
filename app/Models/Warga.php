<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warga extends Model
{
    use HasFactory;

    protected $table = 'warga';

    protected $primaryKey = 'id_warga';

    protected $fillable = [
        'id_pengguna',
        'nik',
        'no_kk',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'alamat',
        'id_dusun',
        'rt',
        'rw',
    ];

    protected function casts(): array
    {
        return [
            'tgl_lahir' => 'date',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function dusun(): BelongsTo
    {
        return $this->belongsTo(Dusun::class, 'id_dusun', 'id_dusun');
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'id_warga', 'id_warga');
    }

    /**
     * Daftar 11 kolom wajib profil kependudukan beserta labelnya.
     *
     * @var array<string, string>
     */
    public static array $daftarFieldWajib = [
        'no_kk' => 'Nomor KK',
        'tempat_lahir' => 'Tempat Lahir',
        'tgl_lahir' => 'Tanggal Lahir',
        'jenis_kelamin' => 'Jenis Kelamin',
        'agama' => 'Agama',
        'status_perkawinan' => 'Status Perkawinan',
        'pekerjaan' => 'Pekerjaan',
        'alamat' => 'Alamat Sesuai KTP',
        'id_dusun' => 'Dusun',
        'rt' => 'RT',
        'rw' => 'RW',
    ];

    /**
     * Mengembalikan daftar kolom wajib yang belum terisi.
     *
     * @return array<string, string>
     */
    public function kolomBelumLengkap(): array
    {
        $belum = [];
        foreach (self::$daftarFieldWajib as $field => $label) {
            if (empty($this->{$field})) {
                $belum[$field] = $label;
            }
        }

        return $belum;
    }

    /**
     * Menghitung persentase kelengkapan profil (0 - 100%).
     */
    public function persentaseKelengkapan(): int
    {
        $total = count(self::$daftarFieldWajib);
        $terisi = $total - count($this->kolomBelumLengkap());

        return (int) round(($terisi / $total) * 100);
    }

    /**
     * Memeriksa apakah profil warga sudah lengkap untuk mengajukan surat.
     * Wajib 11 kolom: no_kk, tempat_lahir, tgl_lahir, jenis_kelamin, agama,
     * status_perkawinan, pekerjaan, alamat, id_dusun, rt, rw.
     */
    public function profilLengkap(): bool
    {
        return count($this->kolomBelumLengkap()) === 0;
    }
}
