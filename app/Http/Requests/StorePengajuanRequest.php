<?php

namespace App\Http\Requests;

use App\Models\Layanan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat permohonan surat.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && $user->role === 'warga' && $user->warga !== null;
    }

    /**
     * Aturan validasi dinamis berdasarkan jenis layanan dan berkas persyaratan.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'id_layanan' => ['required', 'integer', 'exists:layanan,id_layanan'],
            'data_isian' => ['required', 'array'],
            'berkas' => ['required', 'array'],
            'berkas.*' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:2048'],
        ];

        $idLayanan = $this->input('id_layanan');
        if (! $idLayanan) {
            return $rules;
        }

        $layanan = Layanan::find($idLayanan);
        if (! $layanan) {
            return $rules;
        }

        // Aturan validasi data isian dinamis dari config/layanan.php
        $configLayanan = config("layanan.{$layanan->kode}");
        if ($configLayanan && isset($configLayanan['fields'])) {
            foreach ($configLayanan['fields'] as $key => $field) {
                $rules["data_isian.{$key}"] = $field['rules'] ?? ['required', 'string'];
            }
        }

        // Aturan validasi berkas persyaratan aktif
        $syaratAktif = $layanan->syaratAktif;
        foreach ($syaratAktif as $syarat) {
            $fieldKey = "berkas.{$syarat->id_syarat}";
            if ($syarat->wajib) {
                $rules[$fieldKey] = ['required', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:2048'];
            } else {
                $rules[$fieldKey] = ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:2048'];
            }
        }

        return $rules;
    }

    /**
     * Nama atribut dalam Bahasa Indonesia.
     */
    public function attributes(): array
    {
        $attrs = [
            'id_layanan' => 'Jenis Layanan Surat',
            'data_isian' => 'Data Isian Formulir',
            'berkas' => 'Berkas Persyaratan',
            'data_isian.keperluan' => 'Keperluan Surat',
            'data_isian.alamat_domisili' => 'Alamat Domisili',
            'data_isian.lama_tinggal_tahun' => 'Lama Tinggal',
            'data_isian.tujuan' => 'Tujuan / Keperluan SKTM',
            'data_isian.penghasilan_per_bulan' => 'Penghasilan per Bulan',
            'data_isian.jumlah_tanggungan' => 'Jumlah Tanggungan',
            'data_isian.nama_usaha' => 'Nama Usaha',
            'data_isian.jenis_usaha' => 'Jenis Usaha',
            'data_isian.alamat_usaha' => 'Alamat Usaha',
            'data_usaha.lama_usaha_tahun' => 'Lama Usaha',
        ];

        $idLayanan = $this->input('id_layanan');
        if ($idLayanan) {
            $layanan = Layanan::find($idLayanan);
            if ($layanan) {
                foreach ($layanan->syaratAktif as $syarat) {
                    $attrs["berkas.{$syarat->id_syarat}"] = "Berkas {$syarat->nama_syarat}";
                }
            }
        }

        return $attrs;
    }

    /**
     * Pesan kustom validasi.
     */
    public function messages(): array
    {
        return [
            'id_layanan.required' => 'Silakan pilih salah satu jenis layanan surat.',
            'id_layanan.exists' => 'Jenis layanan yang dipilih tidak terdaftar.',
            'berkas.required' => 'Berkas persyaratan wajib diunggah.',
            'berkas.*.mimes' => 'Format berkas harus berupa JPG, PNG, atau PDF.',
            'berkas.*.max' => 'Ukuran setiap berkas maksimal 2048 KB (2 MB).',
            'berkas.*.required' => 'Berkas persyaratan wajib diunggah.',
            'data_isian.*.required' => 'Kolom ini wajib diisi.',
            'data_isian.*.max' => 'Panjang isian melebihi batas yang diizinkan.',
            'data_isian.*.integer' => 'Isian harus berupa bilangan bulat angka.',
            'data_isian.*.min' => 'Nilai isian tidak boleh bernilai negatif.',
        ];
    }
}
