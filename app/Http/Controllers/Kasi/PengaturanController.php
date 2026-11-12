<?php

namespace App\Http\Controllers\Kasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kasi\UpdatePengaturanRequest;
use App\Models\Pengaturan;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    /**
     * Menampilkan halaman formulir pengaturan kampung (Layar K7).
     */
    public function edit(): View
    {
        $profil = Pengaturan::ambil('profil_kampung', [
            'nama_kampung' => 'Rukti Harjo',
            'kecamatan' => 'Seputih Raman',
            'kabupaten' => 'Lampung Tengah',
            'alamat' => 'Jl. Merdeka No. 1, Kampung Rukti Harjo',
            'kode_pos' => '34155',
            'telepon' => '',
            'whatsapp' => '',
        ]);

        $surat = Pengaturan::ambil('surat', [
            'kode_kampung' => 'RH',
            'jabatan_penandatangan' => 'Kepala Kampung',
        ]);

        $jamLayanan = Pengaturan::ambil('jam_layanan', [
            'senin_kamis' => '08.00 - 15.00 WIB',
            'jumat' => '08.00 - 11.30 WIB',
            'catatan' => 'Pelayanan administrasi tatap muka dilayani pada jam kantor, permohonan daring aktif 24 jam.',
        ]);

        $statistik = Pengaturan::ambil('statistik_kampung', [
            'jiwa' => 5990,
            'kk' => 1780,
            'sumber' => 'Laporan Bulanan Kependudukan',
        ]);

        return view('kasi.pengaturan.edit', [
            'profil' => $profil,
            'surat' => $surat,
            'jamLayanan' => $jamLayanan,
            'statistik' => $statistik,
        ]);
    }

    /**
     * Menyimpan perubahan pengaturan kampung.
     */
    public function update(UpdatePengaturanRequest $request): RedirectResponse
    {
        $userId = $request->user()->id_pengguna;

        // 1. Simpan Profil Kampung & Kop
        Pengaturan::simpan('profil_kampung', [
            'nama_kampung' => $request->validated('nama_kampung'),
            'kecamatan' => $request->validated('kecamatan'),
            'kabupaten' => $request->validated('kabupaten'),
            'alamat' => $request->validated('alamat'),
            'kode_pos' => $request->validated('kode_pos'),
            'telepon' => $request->validated('telepon') ?: null,
            'whatsapp' => $request->validated('whatsapp') ?: null,
        ], $userId);

        // 2. Simpan Format Surat
        Pengaturan::simpan('surat', [
            'kode_kampung' => strtoupper(trim($request->validated('kode_kampung'))),
            'jabatan_penandatangan' => $request->validated('jabatan_penandatangan'),
        ], $userId);

        // 3. Simpan Jam Layanan
        Pengaturan::simpan('jam_layanan', [
            'senin_kamis' => $request->validated('jam_senin_kamis'),
            'jumat' => $request->validated('jam_jumat'),
            'catatan' => $request->validated('catatan_layanan') ?: null,
        ], $userId);

        // 4. Simpan Statistik Kampung
        Pengaturan::simpan('statistik_kampung', [
            'jiwa' => (int) $request->validated('jumlah_jiwa', 0),
            'kk' => (int) $request->validated('jumlah_kk', 0),
            'sumber' => $request->validated('sumber_statistik') ?: null,
        ], $userId);

        AuditLogService::catat(
            'Memperbarui Pengaturan Kampung',
            'Pengaturan Kampung',
            $request->validated('nama_kampung'),
            ['kode_kampung' => strtoupper(trim($request->validated('kode_kampung')))]
        );

        return redirect()->route('kasi.pengaturan.edit')
            ->with('status', 'Pengaturan profil kampung dan format surat berhasil diperbarui.');
    }
}
