<?php

namespace App\Http\Requests\Auth;

use App\Models\Pengguna;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $inputIdentifier = trim((string) $this->username);

        // Cari pengguna berdasarkan username (case-insensitive) ATAU NIK kependudukan warga
        $pengguna = Pengguna::whereRaw('LOWER(username) = ?', [Str::lower($inputIdentifier)])
            ->orWhereHas('warga', function ($q) use ($inputIdentifier) {
                $q->where('nik', $inputIdentifier);
            })
            ->first();

        $credentials = [
            'username' => $pengguna ? $pengguna->username : $inputIdentifier,
            'password' => $this->password,
        ];

        // Ingat saya dinonaktifkan (aturan keamanan: kirim false ke remember)
        if (! Auth::attempt($credentials, false)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => 'NIK / Username atau password yang Anda masukkan tidak sesuai.',
            ]);
        }

        // Periksa status_aktif pengguna
        /** @var Pengguna $user */
        $user = Auth::user();
        if (! $user->status_aktif) {
            Auth::logout();

            $this->session()->invalidate();
            $this->session()->regenerateToken();

            throw ValidationException::withMessages([
                'username' => 'Akun Anda dinonaktifkan. Silakan hubungi kantor balai desa.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')).'|'.$this->ip());
    }
}
