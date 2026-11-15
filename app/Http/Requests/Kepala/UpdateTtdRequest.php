<?php

namespace App\Http\Requests\Kepala;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTtdRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'kepala';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file_ttd' => [
                'required',
                'file',
                'mimes:png,jpg,jpeg',
                'max:1024', // maks 1MB
            ],
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
            'file_ttd.required' => 'Silakan pilih berkas gambar tanda tangan Anda.',
            'file_ttd.file' => 'Berkas tanda tangan tidak valid.',
            'file_ttd.mimes' => 'Format berkas tanda tangan harus berupa gambar PNG, JPG, atau JPEG.',
            'file_ttd.max' => 'Ukuran berkas tanda tangan maksimal 1 MB (1024 KB).',
        ];
    }
}
