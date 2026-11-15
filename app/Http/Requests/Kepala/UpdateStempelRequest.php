<?php

namespace App\Http\Requests\Kepala;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStempelRequest extends FormRequest
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
            'file_stempel' => [
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
            'file_stempel.required' => 'Silakan pilih berkas gambar stempel resmi kampung.',
            'file_stempel.file' => 'Berkas stempel tidak valid.',
            'file_stempel.mimes' => 'Format berkas stempel harus berupa gambar PNG, JPG, atau JPEG.',
            'file_stempel.max' => 'Ukuran berkas stempel maksimal 1 MB (1024 KB).',
        ];
    }
}
