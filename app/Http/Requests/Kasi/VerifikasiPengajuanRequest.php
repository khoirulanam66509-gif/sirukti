<?php

namespace App\Http\Requests\Kasi;

use App\Models\Pengajuan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class VerifikasiPengajuanRequest extends FormRequest
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
            'aksi' => ['required', 'string', 'in:proses,minta_perbaikan,ajukan_ttd,tolak'],
            'catatan_petugas' => [
                'nullable',
                'required_if:aksi,tolak',
                function ($attribute, $value, $fail) {
                    if ($this->input('aksi') === 'tolak' && empty(trim((string) $value))) {
                        $fail('Alasan penolakan pengajuan wajib diisi.');
                    }
                },
                'string',
                'max:1000',
            ],
            'berkas' => ['nullable', 'array'],
            'berkas.*.status_verifikasi' => ['nullable', 'string', 'in:menunggu,sesuai,perlu_perbaikan'],
            'berkas.*.catatan' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Konfigurasi validasi lanjutan untuk alur verifikasi berkas Kasi.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $aksi = $this->input('aksi');
            $berkas = $this->input('berkas', []);

            /** @var Pengajuan|null $pengajuan */
            $pengajuan = $this->route('pengajuan');

            if ($aksi === 'minta_perbaikan') {
                $adaPerluPerbaikan = false;
                $berkasModels = $pengajuan ? $pengajuan->berkas()->with('syarat')->get()->keyBy('id_berkas') : collect();

                if (is_array($berkas)) {
                    foreach ($berkas as $idBerkas => $item) {
                        $status = $item['status_verifikasi'] ?? 'menunggu';
                        $catatan = trim((string) ($item['catatan'] ?? ''));

                        if ($status === 'perlu_perbaikan') {
                            $adaPerluPerbaikan = true;
                            if (empty($catatan)) {
                                $namaBerkas = $berkasModels[$idBerkas]?->syarat?->nama_syarat ?? "berkas #{$idBerkas}";
                                $validator->errors()->add(
                                    "berkas.{$idBerkas}.catatan",
                                    "Alasan perbaikan untuk berkas {$namaBerkas} wajib diisi."
                                );
                            }
                        }
                    }
                }

                if (! $adaPerluPerbaikan) {
                    $validator->errors()->add(
                        'berkas',
                        'Tandai minimal satu berkas yang perlu diperbaiki beserta alasannya.'
                    );
                }
            } elseif (in_array($aksi, ['proses', 'ajukan_ttd'], true)) {
                $adaBelumSesuai = false;
                if (is_array($berkas) && ! empty($berkas)) {
                    foreach ($berkas as $item) {
                        $status = $item['status_verifikasi'] ?? 'menunggu';
                        if ($status !== 'sesuai') {
                            $adaBelumSesuai = true;
                            break;
                        }
                    }
                } else {
                    if ($pengajuan) {
                        $adaBelumSesuai = $pengajuan->berkas()
                            ->where('status_verifikasi', '!=', 'sesuai')
                            ->exists();
                    }
                }

                if ($adaBelumSesuai) {
                    $validator->errors()->add(
                        'berkas',
                        'Seluruh berkas persyaratan harus diverifikasi Sesuai sebelum pengajuan dapat diproses atau diajukan ke Kepala Kampung.'
                    );
                }
            }
        });
    }

    /**
     * Pesan kustom validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'aksi.required' => 'Pilihan tindakan verifikasi wajib ditentukan.',
            'aksi.in' => 'Tindakan verifikasi yang dipilih tidak valid.',
            'catatan_petugas.required' => 'Alasan penolakan pengajuan wajib diisi.',
            'catatan_petugas.required_if' => 'Alasan penolakan pengajuan wajib diisi.',
            'catatan_petugas.max' => 'Catatan petugas tidak boleh lebih dari 1000 karakter.',
            'berkas.array' => 'Data verifikasi berkas tidak valid.',
            'berkas.*.status_verifikasi.in' => 'Status verifikasi berkas yang dipilih tidak valid.',
            'berkas.*.catatan.max' => 'Catatan alasan perbaikan tidak boleh lebih dari 255 karakter.',
        ];
    }
}
