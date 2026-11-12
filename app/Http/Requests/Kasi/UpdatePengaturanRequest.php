<?php

namespace App\Http\Requests\Kasi;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePengaturanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'kasi';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Profil Kampung & Kop Surat
            'nama_kampung' => ['required', 'string', 'max:100'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'kabupaten' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string', 'max:255'],
            'kode_pos' => ['required', 'string', 'max:10'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],

            // Format Surat
            'kode_kampung' => ['required', 'string', 'max:10'],
            'jabatan_penandatangan' => ['required', 'string', 'max:60'],

            // Jam Layanan
            'jam_senin_kamis' => ['required', 'string', 'max:60'],
            'jam_jumat' => ['required', 'string', 'max:60'],
            'catatan_layanan' => ['nullable', 'string', 'max:255'],

            // Statistik Wilayah
            'jumlah_jiwa' => ['nullable', 'integer', 'min:0'],
            'jumlah_kk' => ['nullable', 'integer', 'min:0'],
            'sumber_statistik' => ['nullable', 'string', 'max:150'],
        ];
    }

    /**
     * Pesan validasi kustom berbahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_kampung.required' => 'Nama kampung wajib diisi.',
            'kecamatan.required' => 'Nama kecamatan wajib diisi.',
            'kabupaten.required' => 'Nama kabupaten wajib diisi.',
            'alamat.required' => 'Alamat kantor kampung wajib diisi.',
            'kode_pos.required' => 'Kode pos wajib diisi.',
            'kode_kampung.required' => 'Kode kampung untuk penomoran surat wajib diisi.',
            'jabatan_penandatangan.required' => 'Sebutan jabatan penandatangan wajib diisi.',
            'jam_senin_kamis.required' => 'Jam pelayanan Senin - Kamis wajib diisi.',
            'jam_jumat.required' => 'Jam pelayanan Jumat wajib diisi.',
        ];
    }
}
