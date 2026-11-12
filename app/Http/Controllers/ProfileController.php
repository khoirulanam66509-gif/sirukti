<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Dusun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan formulir profil warga.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Pastikan relasi warga tersedia
        $warga = $user->warga;
        if (! $warga && $user->role === 'warga') {
            $warga = $user->warga()->create([
                'nik' => $user->username,
            ]);
        }

        $daftarDusun = Dusun::orderBy('nama_dusun')->get();

        return view('profile.edit', [
            'user' => $user,
            'warga' => $warga,
            'daftarDusun' => $daftarDusun,
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna dan data kependudukan warga.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        DB::transaction(function () use ($user, $validated) {
            // Update tabel pengguna
            $user->update([
                'nama' => $validated['nama'],
                'username' => Str::lower(trim($validated['username'])),
                'no_hp' => $validated['no_hp'],
            ]);

            // Format RT dan RW menjadi 3 digit (contoh: 3 -> 003) bila terisi
            $rt = ! empty($validated['rt']) ? str_pad(ltrim($validated['rt'], '0') ?: '0', 3, '0', STR_PAD_LEFT) : null;
            $rw = ! empty($validated['rw']) ? str_pad(ltrim($validated['rw'], '0') ?: '0', 3, '0', STR_PAD_LEFT) : null;

            // Update tabel warga (hanya milik user yang login, NIK tidak disentuh)
            if ($user->warga) {
                $user->warga->update([
                    'no_kk' => $validated['no_kk'] ?? null,
                    'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                    'tgl_lahir' => $validated['tgl_lahir'] ?? null,
                    'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                    'agama' => $validated['agama'] ?? null,
                    'status_perkawinan' => $validated['status_perkawinan'] ?? null,
                    'pekerjaan' => $validated['pekerjaan'] ?? null,
                    'alamat' => $validated['alamat'] ?? null,
                    'id_dusun' => $validated['id_dusun'] ?? null,
                    'rt' => $rt,
                    'rw' => $rw,
                ]);
            }
        });

        return redirect()->route('profile.edit')->with('success', 'Data profil Anda telah berhasil disimpan.');
    }
}
