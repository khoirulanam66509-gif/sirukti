<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Menampilkan formulir ubah password (Layar U1).
     */
    public function edit(Request $request): View
    {
        return view('auth.ubah-password', [
            'user' => $request->user(),
            'wajibGanti' => (bool) $request->user()->wajib_ganti_password,
        ]);
    }

    /**
     * Memproses pembaruan kata sandi pengguna.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini yang Anda masukkan salah.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.different' => 'Kata sandi baru harus berbeda dari kata sandi saat ini.',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
            'wajib_ganti_password' => false,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Kata sandi berhasil diperbarui. Silakan lanjutkan aktivitas Anda.');
    }
}
