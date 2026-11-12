<?php

namespace App\Http\Requests\Kasi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSyaratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'kasi';
    }

    public function rules(): array
    {
        return [
            'nama_syarat' => ['required', 'string', 'max:100'],
            'petunjuk' => ['nullable', 'string', 'max:255'],
            'wajib' => ['nullable', 'boolean'],
            'urutan' => ['nullable', 'integer', 'min:1'],
            'status_aktif' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_syarat.required' => 'Nama berkas persyaratan wajib diisi.',
            'urutan.integer' => 'Urutan berkas harus berupa angka bulat positif.',
        ];
    }
}
