<?php

namespace App\Http\Requests\Kasi;

use App\Models\Warga;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateWargaRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'kasi';
    }

    /**
     * Aturan validasi untuk pembaruan data warga oleh Kasi.
     */
    public function rules(): array
    {
        /** @var Warga|null $warga */
        $warga = $this->route('warga');
        $targetUserId = $warga?->pengguna?->id_pengguna;

        return [
            'nama' => ['required', 'string', 'max:100'],
            'username' => [
                'nullable',
                'string',
                'min:4',
                'max:30',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('pengguna', 'username')->ignore($targetUserId, 'id_pengguna'),
                function ($attribute, $value, $fail) use ($warga) {
                    if (empty($value)) {
                        return;
                    }
                    $clean = Str::lower(trim((string) $value));
                    $reserved = ['admin', 'kasi', 'kepala', 'root', 'system', 'petugas', 'operator', 'sirukti', 'kampung', 'desa', 'superadmin'];
                    if (in_array($clean, $reserved, true)) {
                        $fail('Username yang dipilih tidak diizinkan karena merupakan kata sistem.');
                    }

                    if (preg_match('/^[0-9]{16}$/', $clean)) {
                        if ($warga && $clean !== $warga->nik) {
                            $fail('Username berupa 16 digit angka hanya diperbolehkan jika sama dengan NIK warga.');
                        }
                    }
                },
            ],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'no_kk' => ['nullable', 'string', 'size:16', 'regex:/^[0-9]{16}$/'],
            'tempat_lahir' => ['nullable', 'string', 'max:50'],
            'tgl_lahir' => ['nullable', 'date', 'before_or_equal:today'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'agama' => ['nullable', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu'],
            'status_perkawinan' => ['nullable', 'in:belum_kawin,kawin,cerai_hidup,cerai_mati'],
            'pekerjaan' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'id_dusun' => ['nullable', 'exists:dusun,id_dusun'],
            'rt' => ['nullable', 'string', 'max:3'],
            'rw' => ['nullable', 'string', 'max:3'],
        ];
    }

    /**
     * Pesan kustom dalam bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama lengkap warga wajib diisi.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.max' => 'Username maksimal 30 karakter.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, dan garis bawah (_).',
            'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'no_kk.size' => 'Nomor Kartu Keluarga (KK) harus tepat 16 digit angka.',
            'no_kk.regex' => 'Nomor Kartu Keluarga hanya boleh berisi digit angka.',
            'tgl_lahir.before_or_equal' => 'Tanggal lahir tidak boleh melebihi hari ini.',
            'id_dusun.exists' => 'Pilihan dusun tidak valid.',
        ];
    }
}
