<?php

namespace App\Http\Requests\Kepala;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilKepalaRequest extends FormRequest
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
            'nama' => ['required', 'string', 'max:100'],
            'nip' => ['nullable', 'string', 'max:25'],
            'jabatan' => ['required', 'string', 'max:60'],
            'no_hp' => ['nullable', 'string', 'max:20'],
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
            'nama.required' => 'Nama Kepala Kampung wajib diisi.',
            'jabatan.required' => 'Jabatan resmi wajib diisi.',
        ];
    }
}
