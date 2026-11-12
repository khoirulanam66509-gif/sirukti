<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Siapkan data sebelum divalidasi (fallback username jika tidak disertakan).
     */
    protected function prepareForValidation(): void
    {
        if (! $this->has('username') && $this->user()) {
            $this->merge([
                'username' => $this->user()->username,
            ]);
        }
    }

    /**
     * Aturan validasi untuk pembaruan profil kependudukan.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $userId = $user->id_pengguna;

        return [
            // Kolom dari tabel pengguna
            'nama' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'string',
                'min:4',
                'max:30',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('pengguna', 'username')->ignore($userId, 'id_pengguna'),
                function ($attribute, $value, $fail) use ($user) {
                    $clean = Str::lower(trim((string) $value));
                    $reserved = ['admin', 'kasi', 'kepala', 'root', 'system', 'petugas', 'operator', 'sirukti', 'kampung', 'desa', 'superadmin'];
                    if (in_array($clean, $reserved, true)) {
                        $fail('Username yang dipilih tidak diizinkan karena merupakan kata sistem.');
                    }

                    // Cek jika 16 digit angka: hanya boleh jika sama dengan NIK milik sendiri
                    if (preg_match('/^[0-9]{16}$/', $clean)) {
                        $nikMilikSendiri = $user->warga?->nik ?? $user->username;
                        if ($clean !== $nikMilikSendiri) {
                            $fail('Username berupa 16 digit angka hanya diperbolehkan jika sama dengan NIK Anda sendiri.');
                        }
                    }
                },
            ],
            'no_hp' => [
                'required',
                'string',
                'regex:/^[0-9+\-\s]{9,20}$/',
                Rule::unique('pengguna', 'no_hp')->ignore($userId, 'id_pengguna'),
            ],

            // Kolom dari tabel warga
            'no_kk' => ['nullable', 'string', 'size:16', 'regex:/^[0-9]{16}$/'],
            'tempat_lahir' => ['nullable', 'string', 'max:50'],
            'tgl_lahir' => ['nullable', 'date', 'before:today'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'agama' => ['nullable', 'string', 'max:20', Rule::in(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu', 'Konghucu'])],
            'status_perkawinan' => ['nullable', Rule::in(['belum_kawin', 'kawin', 'cerai_hidup', 'cerai_mati'])],
            'pekerjaan' => ['nullable', 'string', 'max:60'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'id_dusun' => ['nullable', 'integer', 'exists:dusun,id_dusun'],
            'rt' => ['nullable', 'string', 'max:3', 'regex:/^[0-9]{1,3}$/'],
            'rw' => ['nullable', 'string', 'max:3', 'regex:/^[0-9]{1,3}$/'],
        ];
    }

    /**
     * Nama atribut dalam bahasa Indonesia.
     */
    public function attributes(): array
    {
        return [
            'nama' => 'Nama Lengkap',
            'username' => 'Username',
            'no_hp' => 'Nomor WhatsApp / Seluler',
            'no_kk' => 'Nomor Kartu Keluarga (KK)',
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
    }

    /**
     * Pesan kustom validasi.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.max' => 'Username maksimal 30 karakter.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, dan garis bawah (_).',
            'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'no_hp.required' => 'Nomor WhatsApp / seluler wajib diisi.',
            'no_hp.unique' => 'Nomor WhatsApp / seluler sudah digunakan oleh warga lain.',
            'no_hp.regex' => 'Format nomor WhatsApp / seluler tidak valid.',
            'no_kk.size' => 'Nomor Kartu Keluarga harus tepat 16 digit.',
            'no_kk.regex' => 'Nomor Kartu Keluarga hanya boleh berisi 16 digit angka.',
            'tgl_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'tgl_lahir.date' => 'Format tanggal lahir tidak valid.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            'agama.in' => 'Pilihan agama tidak valid.',
            'status_perkawinan.in' => 'Pilihan status perkawinan tidak valid.',
            'id_dusun.exists' => 'Dusun yang dipilih tidak terdaftar di sistem.',
            'rt.regex' => 'Nomor RT harus berupa angka maksimal 3 digit (contoh: 003).',
            'rw.regex' => 'Nomor RW harus berupa angka maksimal 3 digit (contoh: 001).',
        ];
    }
}
