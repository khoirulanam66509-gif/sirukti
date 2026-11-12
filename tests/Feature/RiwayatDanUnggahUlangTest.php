<?php

namespace Tests\Feature;

use App\Models\Dusun;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\Surat;
use App\Models\SyaratLayanan;
use App\Models\Warga;
use App\Services\PengajuanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RiwayatDanUnggahUlangTest extends TestCase
{
    use RefreshDatabase;

    private Dusun $dusun;

    private Layanan $layanan;

    private SyaratLayanan $syaratKtp;

    private SyaratLayanan $syaratKk;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->dusun = Dusun::create([
            'nama_dusun' => 'Dusun I A',
        ]);

        $this->layanan = Layanan::create([
            'kode' => 'SKCK',
            'nama_layanan' => 'Pengantar SKCK',
            'deskripsi' => 'Surat pengantar kepolisian',
            'status_aktif' => true,
            'urutan' => 1,
        ]);

        $this->syaratKtp = SyaratLayanan::create([
            'id_layanan' => $this->layanan->id_layanan,
            'nama_syarat' => 'Foto KTP',
            'petunjuk' => 'Foto KTP asli',
            'wajib' => true,
            'urutan' => 1,
            'status_aktif' => true,
        ]);

        $this->syaratKk = SyaratLayanan::create([
            'id_layanan' => $this->layanan->id_layanan,
            'nama_syarat' => 'Foto KK',
            'petunjuk' => 'Foto KK asli',
            'wajib' => true,
            'urutan' => 2,
            'status_aktif' => true,
        ]);
    }

    private function createWargaUser(string $nik = '1802010101900001', string $noHp = '081234567890'): Pengguna
    {
        $user = Pengguna::create([
            'nama' => 'Sri Wahyuni',
            'username' => $nik,
            'password' => bcrypt('password123'),
            'no_hp' => $noHp,
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        Warga::create([
            'id_pengguna' => $user->id_pengguna,
            'nik' => $nik,
            'no_kk' => '1802010101900002',
            'tempat_lahir' => 'Lampung Tengah',
            'tgl_lahir' => '1990-05-15',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Petani',
            'alamat' => 'Jl. Poros Utama No. 12',
            'id_dusun' => $this->dusun->id_dusun,
            'rt' => '001',
            'rw' => '001',
        ]);

        return $user;
    }

    public function test_riwayat_hanya_menampilkan_pengajuan_milik_warga_sendiri(): void
    {
        $userA = $this->createWargaUser('1802010101900001', '081200000001');
        $userB = $this->createWargaUser('1802010101900002', '081200000002');

        $service = app(PengajuanService::class);

        // Pengajuan A
        $pA = $service->buat($userA->warga, $this->layanan, ['keperluan' => 'Milik Warga A'], [
            $this->syaratKtp->id_syarat => UploadedFile::fake()->image('ktpA.jpg'),
            $this->syaratKk->id_syarat => UploadedFile::fake()->image('kkA.jpg'),
        ], $userA);

        // Pengajuan B
        $pB = $service->buat($userB->warga, $this->layanan, ['keperluan' => 'Milik Warga B'], [
            $this->syaratKtp->id_syarat => UploadedFile::fake()->image('ktpB.jpg'),
            $this->syaratKk->id_syarat => UploadedFile::fake()->image('kkB.jpg'),
        ], $userB);

        $response = $this->actingAs($userA)->get('/riwayat');

        $response->assertOk();
        $response->assertSee($pA->no_pengajuan);
        $response->assertDontSee($pB->no_pengajuan);
    }

    public function test_filter_dan_pencarian_riwayat_berfungsi(): void
    {
        $user = $this->createWargaUser();
        $service = app(PengajuanService::class);

        $p1 = $service->buat($user->warga, $this->layanan, ['keperluan' => 'Surat Pertama'], [
            $this->syaratKtp->id_syarat => UploadedFile::fake()->image('ktp.jpg'),
            $this->syaratKk->id_syarat => UploadedFile::fake()->image('kk.jpg'),
        ], $user);

        // Update status pengajuan menjadi selesai
        $p1->update(['status' => 'selesai']);

        // Filter status selesai
        $responseSelesai = $this->actingAs($user)->get('/riwayat?status=selesai');
        $responseSelesai->assertOk();
        $responseSelesai->assertSee($p1->no_pengajuan);

        // Filter status ditolak (harus kosong)
        $responseDitolak = $this->actingAs($user)->get('/riwayat?status=ditolak');
        $responseDitolak->assertOk();
        $responseDitolak->assertDontSee($p1->no_pengajuan);

        // Pencarian nomor
        $responseCari = $this->actingAs($user)->get("/riwayat?q={$p1->no_pengajuan}");
        $responseCari->assertOk();
        $responseCari->assertSee($p1->no_pengajuan);
    }

    public function test_detail_pengajuan_hanya_dapat_diakses_oleh_pemilik_atau_petugas(): void
    {
        $userOwner = $this->createWargaUser('1802010101900001', '081200000001');
        $userStranger = $this->createWargaUser('1802010101900002', '081200000002');
        $service = app(PengajuanService::class);

        $pengajuan = $service->buat($userOwner->warga, $this->layanan, ['keperluan' => 'Uji Detail'], [
            $this->syaratKtp->id_syarat => UploadedFile::fake()->image('ktp.jpg'),
            $this->syaratKk->id_syarat => UploadedFile::fake()->image('kk.jpg'),
        ], $userOwner);

        // Pemilik dapat mengakses
        $responseOwner = $this->actingAs($userOwner)->get("/pengajuan/{$pengajuan->id_pengajuan}");
        $responseOwner->assertOk();
        $responseOwner->assertSee($pengajuan->no_pengajuan);

        // Warga lain dilarang (403)
        $responseStranger = $this->actingAs($userStranger)->get("/pengajuan/{$pengajuan->id_pengajuan}");
        $responseStranger->assertForbidden();

        // Kasi berhak mengakses
        $kasi = Pengguna::create([
            'nama' => 'Petugas Kasi',
            'username' => 'kasi_user',
            'password' => bcrypt('password123'),
            'role' => 'kasi',
            'status_aktif' => true,
        ]);
        $responseKasi = $this->actingAs($kasi)->get("/pengajuan/{$pengajuan->id_pengajuan}");
        $responseKasi->assertOk();
    }

    public function test_unggah_ulang_hanya_untuk_berkas_perlu_perbaikan_dan_mengubah_status_pengajuan(): void
    {
        $user = $this->createWargaUser();
        $service = app(PengajuanService::class);

        $pengajuan = $service->buat($user->warga, $this->layanan, ['keperluan' => 'Uji Kurang Berkas'], [
            $this->syaratKtp->id_syarat => UploadedFile::fake()->image('ktp.jpg'),
            $this->syaratKk->id_syarat => UploadedFile::fake()->image('kk.jpg'),
        ], $user);

        $berkasKtp = $pengajuan->berkas()->where('id_syarat', $this->syaratKtp->id_syarat)->first();
        $berkasKk = $pengajuan->berkas()->where('id_syarat', $this->syaratKk->id_syarat)->first();

        // Simulasikan Kasi memverifikasi: KTP sesuai, KK perlu perbaikan
        $berkasKtp->update(['status_verifikasi' => 'sesuai']);
        $berkasKk->update([
            'status_verifikasi' => 'perlu_perbaikan',
            'catatan' => 'Foto KK buram dan terpotong',
        ]);
        $pengajuan->update([
            'status' => 'kurang_berkas',
            'catatan_petugas' => 'Perbaiki foto KK',
        ]);

        // 1. Mencoba mengunggah ulang berkas KTP (yang statusnya sudah 'sesuai') -> harus ditolak
        $responseGagal = $this->actingAs($user)->post("/pengajuan/{$pengajuan->id_pengajuan}/unggah-ulang", [
            'berkas' => [
                $berkasKtp->id_berkas => UploadedFile::fake()->image('ktp_baru.jpg'),
            ],
        ]);
        $responseGagal->assertSessionHasErrors(["berkas.{$berkasKtp->id_berkas}"]);

        // 2. Mengunggah berkas pengganti untuk KK yang 'perlu_perbaikan'
        $fileKkBaru = UploadedFile::fake()->image('kk_jelas.jpg')->size(400);

        $responseSukses = $this->actingAs($user)->post("/pengajuan/{$pengajuan->id_pengajuan}/unggah-ulang", [
            'berkas' => [
                $berkasKk->id_berkas => $fileKkBaru,
            ],
        ]);

        $responseSukses->assertRedirect("/pengajuan/{$pengajuan->id_pengajuan}");
        $responseSukses->assertSessionHas('success');

        // Pastikan status berkas KK kembali menjadi 'menunggu'
        $berkasKk->refresh();
        $this->assertEquals('menunggu', $berkasKk->status_verifikasi);
        $this->assertEquals('kk_jelas.jpg', $berkasKk->nama_file_asli);

        // Pastikan status pengajuan berubah dari 'kurang_berkas' menjadi 'menunggu'
        $pengajuan->refresh();
        $this->assertEquals('menunggu', $pengajuan->status);

        // Pastikan trigger mencatat status_log transisi status
        $this->assertDatabaseHas('status_log', [
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'status_lama' => 'kurang_berkas',
            'status_baru' => 'menunggu',
            'id_pengguna' => $user->id_pengguna,
        ]);
    }

    public function test_unduh_surat_hanya_dapat_diakses_oleh_pemilik_saat_status_selesai(): void
    {
        $userOwner = $this->createWargaUser('1802010101900001', '081200000001');
        $userStranger = $this->createWargaUser('1802010101900002', '081200000002');
        $service = app(PengajuanService::class);

        $pengajuan = $service->buat($userOwner->warga, $this->layanan, ['keperluan' => 'Uji Surat'], [
            $this->syaratKtp->id_syarat => UploadedFile::fake()->image('ktp.jpg'),
            $this->syaratKk->id_syarat => UploadedFile::fake()->image('kk.jpg'),
        ], $userOwner);

        // 1. Saat status masih 'menunggu', unduh surat dilarang (403)
        $responseBelumSelesai = $this->actingAs($userOwner)->get("/pengajuan/{$pengajuan->id_pengajuan}/unduh-surat");
        $responseBelumSelesai->assertForbidden();

        // 2. Terbitkan surat dan ubah status pengajuan ke 'selesai'
        $pdfPath = 'surat/dummy_surat.pdf';
        Storage::disk('local')->put($pdfPath, '%PDF-1.4 dummy pdf content');

        Surat::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'no_surat' => '140/001/RH/X/2026',
            'tgl_surat' => now(),
            'kode_verifikasi' => 'VRF-2026-0001',
            'file_pdf' => $pdfPath,
            'id_penandatangan' => $userOwner->id_pengguna,
            'ditandatangani_at' => now(),
        ]);
        $pengajuan->update(['status' => 'selesai']);

        // 3. Warga lain mencoba mengunduh surat -> 403 Forbidden
        $responseStranger = $this->actingAs($userStranger)->get("/pengajuan/{$pengajuan->id_pengajuan}/unduh-surat");
        $responseStranger->assertForbidden();

        // 4. Pemilik mengunduh surat -> 200 Download
        $responseOwner = $this->actingAs($userOwner)->get("/pengajuan/{$pengajuan->id_pengajuan}/unduh-surat");
        $responseOwner->assertOk();
        $responseOwner->assertHeader('content-disposition');
    }

    public function test_detail_pengajuan_memuat_tombol_whatsapp_dan_elemen_perbaikan_11(): void
    {
        $user = $this->createWargaUser();
        $service = app(PengajuanService::class);

        $pengajuan = $service->buat($user->warga, $this->layanan, ['keperluan' => 'Uji WA Detail'], [
            $this->syaratKtp->id_syarat => UploadedFile::fake()->image('ktp.jpg'),
            $this->syaratKk->id_syarat => UploadedFile::fake()->image('kk.jpg'),
        ], $user);

        $response = $this->actingAs($user)->get("/pengajuan/{$pengajuan->id_pengajuan}");

        $response->assertOk();
        // Memuat tombol Hubungi Balai WhatsApp di topbar dan kartu bantuan
        $response->assertSee('Hubungi Balai');
        $response->assertSee('wa.me');
        $response->assertSee('Chat WA');
        $response->assertSee($pengajuan->no_pengajuan);

        // Memuat tab Riwayat dan Berkas
        $response->assertSee('Riwayat &amp; Progres Surat', false);
        $response->assertSee('Berkas Persyaratan Dokumen');
    }
}
