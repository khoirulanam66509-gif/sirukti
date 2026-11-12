<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Warga;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan form pendaftaran warga (L2 Daftar).
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Menangani pendaftaran warga baru dalam satu transaksi database.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'nik' => ['required', 'string', 'digits:16', 'regex:/^[0-9]{16}$/', 'unique:pengguna,username', 'unique:warga,nik'],
            'no_hp' => ['required', 'string', 'min:10', 'max:16', 'regex:/^08[0-9]{8,13}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi sesuai KTP.',
            'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'nik.digits' => 'Nomor Induk Kependudukan (NIK) harus tepat 16 angka.',
            'nik.regex' => 'NIK hanya boleh berupa angka 16 digit.',
            'nik.unique' => 'NIK ini sudah terdaftar dalam sistem SIRUKTI.',
            'no_hp.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'no_hp.regex' => 'Nomor WhatsApp / HP harus diawali dengan 08 (contoh: 081234567890).',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $pengguna = DB::transaction(function () use ($request) {
            $pengguna = Pengguna::create([
                'nama' => trim($request->nama),
                'username' => $request->nik,
                'password' => Hash::make($request->password),
                'no_hp' => $request->no_hp,
                'role' => 'warga',
                'status_aktif' => true,
                'wajib_ganti_password' => false,
            ]);

            Warga::create([
                'id_pengguna' => $pengguna->id_pengguna,
                'nik' => $request->nik,
            ]);

            return $pengguna;
        });

        event(new Registered($pengguna));

        Auth::login($pengguna);

        return redirect(route('dashboard', absolute: false));
    }
}
