<?php

namespace Tests\Feature;

use App\Models\BerkasPengajuan;
use App\Models\Dusun;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\SyaratLayanan;
use App\Models\Warga;
use App\Services\BerkasService;
use App\Services\PengajuanService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengajuanTest extends TestCase
{
    use RefreshDatabase;

    private Dusun $dusun;

    private Layanan $layananSkck;

    private SyaratLayanan $syaratKtp;

    private SyaratLayanan $syaratKk;

    private SyaratLayanan $syaratPengantar;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->dusun = Dusun::create([
            'nama_dusun' => 'Dusun I A',
        ]);

        $this->layananSkck = Layanan::create([
            'kode' => 'SKCK',
            'nama_layanan' => 'Pengantar SKCK',
            'deskripsi' => 'Surat pengantar untuk kepolisian',
            'status_aktif' => true,
            'urutan' => 1,
        ]);

        $this->syaratKtp = SyaratLayanan::create([
            'id_layanan' => $this->layananSkck->id_layanan,
            'nama_syarat' => 'Foto KTP',
            'petunjuk' => 'Foto KTP asli',
            'wajib' => true,
            'urutan' => 1,
            'status_aktif' => true,
        ]);

        $this->syaratKk = SyaratLayanan::create([
            'id_layanan' => $this->layananSkck->id_layanan,
            'nama_syarat' => 'Foto KK',
            'petunjuk' => 'Foto KK asli',
            'wajib' => true,
            'urutan' => 2,
            'status_aktif' => true,
        ]);

        $this->syaratPengantar = SyaratLayanan::create([
            'id_layanan' => $this->layananSkck->id_layanan,
            'nama_syarat' => 'Pengantar RT/RW',
            'petunjuk' => 'Foto pengantar RT',
            'wajib' => false, // opsional untuk uji variasi
            'urutan' => 3,
            'status_aktif' => true,
        ]);
    }

    private function createWargaUser(bool $lengkap = true, string $nik = '1802010101900001', string $noHp = '081234567890'): Pengguna
    {
        $user = Pengguna::create([
            'nama' => 'Sri Wahyuni',
            'username' => $nik,
            'password' => bcrypt('password123'),
            'no_hp' => $noHp,
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $wargaData = [
            'id_pengguna' => $user->id_pengguna,
            'nik' => $nik,
        ];

        if ($lengkap) {
            $wargaData = array_merge($wargaData, [
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
        }

        Warga::create($wargaData);

        return $user;
    }

    public function test_warga_belum_lengkap_profil_diarahkan_ke_profil_saat_akses_halaman_pengajuan(): void
    {
        $user = $this->createWargaUser(lengkap: false);

        $response = $this->actingAs($user)->get('/pengajuan/buat');

        $response->assertRedirect('/profil');
        $response->assertSessionHas('warning');
    }

    public function test_halaman_pengajuan_dapat_diakses_oleh_warga_dengan_profil_lengkap(): void
    {
        $user = $this->createWargaUser(lengkap: true);

        $response = $this->actingAs($user)->get('/pengajuan/buat');

        $response->assertOk();
        $response->assertSee('Ajukan Surat Baru');
        $response->assertSee('Pengantar SKCK');
        $response->assertSee('Langkah 1 dari 4');
    }

    public function test_pengajuan_surat_berhasil_disimpan_dan_menghasilkan_nomor_berurutan(): void
    {
        $user = $this->createWargaUser(lengkap: true);

        $fileKtp = UploadedFile::fake()->image('ktp.jpg', 600, 400)->size(500);
        $fileKk = UploadedFile::fake()->create('kk.pdf', 300, 'application/pdf');

        $payload = [
            'id_layanan' => $this->layananSkck->id_layanan,
            'data_isian' => [
                'keperluan' => 'Melamar pekerjaan BUMN',
            ],
            'berkas' => [
                $this->syaratKtp->id_syarat => $fileKtp,
                $this->syaratKk->id_syarat => $fileKk,
            ],
        ];

        $response = $this->actingAs($user)->post('/pengajuan', $payload);

        $response->assertRedirect('/riwayat');
        $response->assertSessionHas('success');

        $todayPrefix = 'PG-'.now()->format('Ymd').'-0001';

        $this->assertDatabaseHas('pengajuan', [
            'no_pengajuan' => $todayPrefix,
            'id_warga' => $user->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'status' => 'menunggu',
            'id_pengubah' => $user->id_pengguna,
        ]);

        $pengajuan = Pengajuan::where('no_pengajuan', $todayPrefix)->first();
        $this->assertNotNull($pengajuan);
        $this->assertEquals(['keperluan' => 'Melamar pekerjaan BUMN'], $pengajuan->data_isian);

        // Pastikan berkas tersimpan di database dan storage privat
        $this->assertDatabaseCount('berkas_pengajuan', 2);
        $berkasKtp = BerkasPengajuan::where('id_pengajuan', $pengajuan->id_pengajuan)
            ->where('id_syarat', $this->syaratKtp->id_syarat)
            ->first();

        $this->assertNotNull($berkasKtp);
        $this->assertEquals('ktp.jpg', $berkasKtp->nama_file_asli);
        Storage::disk('local')->assertExists($berkasKtp->path_file);

        // Pastikan trigger trg_pengajuan_ai mencatat log status awal
        $this->assertDatabaseHas('status_log', [
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'status_lama' => null,
            'status_baru' => 'menunggu',
            'id_pengguna' => $user->id_pengguna,
            'catatan' => 'Pengajuan dibuat',
        ]);
    }

    public function test_nomor_pengajuan_berikutnya_meningkat_berurutan(): void
    {
        $user1 = $this->createWargaUser(true, '1802010101900001', '081200000001');
        $user2 = $this->createWargaUser(true, '1802010101900002', '081200000002');

        $service = app(PengajuanService::class);

        $fileKtp = UploadedFile::fake()->image('ktp.jpg')->size(100);
        $fileKk = UploadedFile::fake()->image('kk.jpg')->size(100);

        $p1 = $service->buat($user1->warga, $this->layananSkck, ['keperluan' => 'Keperluan 1'], [
            $this->syaratKtp->id_syarat => $fileKtp,
            $this->syaratKk->id_syarat => $fileKk,
        ], $user1);

        // Buat pengajuan kedua oleh warga lain
        $fileKtp2 = UploadedFile::fake()->image('ktp2.jpg')->size(100);
        $fileKk2 = UploadedFile::fake()->image('kk2.jpg')->size(100);

        $p2 = $service->buat($user2->warga, $this->layananSkck, ['keperluan' => 'Keperluan 2'], [
            $this->syaratKtp->id_syarat => $fileKtp2,
            $this->syaratKk->id_syarat => $fileKk2,
        ], $user2);

        $todayPrefix = 'PG-'.now()->format('Ymd').'-';
        $this->assertEquals($todayPrefix.'0001', $p1->no_pengajuan);
        $this->assertEquals($todayPrefix.'0002', $p2->no_pengajuan);
    }

    public function test_pengajuan_ditolak_bila_syarat_wajib_kurang(): void
    {
        $user = $this->createWargaUser(lengkap: true);

        // Hanya mengunggah KTP, tanpa KK (padahal KK wajib)
        $fileKtp = UploadedFile::fake()->image('ktp.jpg')->size(200);

        $payload = [
            'id_layanan' => $this->layananSkck->id_layanan,
            'data_isian' => [
                'keperluan' => 'Melamar pekerjaan',
            ],
            'berkas' => [
                $this->syaratKtp->id_syarat => $fileKtp,
            ],
        ];

        $response = $this->actingAs($user)->post('/pengajuan', $payload);

        $response->assertSessionHasErrors(["berkas.{$this->syaratKk->id_syarat}"]);
        $this->assertDatabaseCount('pengajuan', 0);
    }

    public function test_pengajuan_ditolak_bila_tipe_berkas_salah_atau_ukuran_melebihi(): void
    {
        $user = $this->createWargaUser(lengkap: true);

        // File format .txt tidak diizinkan
        $fileTxt = UploadedFile::fake()->create('ktp.txt', 100, 'text/plain');
        // File ukuran > 2048 KB (misal 3000 KB)
        $fileLarge = UploadedFile::fake()->create('kk.pdf', 3000, 'application/pdf');

        $payload = [
            'id_layanan' => $this->layananSkck->id_layanan,
            'data_isian' => [
                'keperluan' => 'Melamar pekerjaan',
            ],
            'berkas' => [
                $this->syaratKtp->id_syarat => $fileTxt,
                $this->syaratKk->id_syarat => $fileLarge,
            ],
        ];

        $response = $this->actingAs($user)->post('/pengajuan', $payload);

        $response->assertSessionHasErrors([
            "berkas.{$this->syaratKtp->id_syarat}",
            "berkas.{$this->syaratKk->id_syarat}",
        ]);
        $this->assertDatabaseCount('pengajuan', 0);
    }

    public function test_pengajuan_ganda_pada_layanan_aktif_ditolak(): void
    {
        $user = $this->createWargaUser(lengkap: true);
        $service = app(PengajuanService::class);

        $fileKtp = UploadedFile::fake()->image('ktp.jpg')->size(100);
        $fileKk = UploadedFile::fake()->image('kk.jpg')->size(100);

        // Buat pengajuan pertama status menunggu (aktif)
        $service->buat($user->warga, $this->layananSkck, ['keperluan' => 'Pertama'], [
            $this->syaratKtp->id_syarat => $fileKtp,
            $this->syaratKk->id_syarat => $fileKk,
        ], $user);

        // Mencoba mengajukan kedua kalinya pada layanan yang sama
        $fileKtp2 = UploadedFile::fake()->image('ktp2.jpg')->size(100);
        $fileKk2 = UploadedFile::fake()->image('kk2.jpg')->size(100);

        $payload = [
            'id_layanan' => $this->layananSkck->id_layanan,
            'data_isian' => [
                'keperluan' => 'Kedua',
            ],
            'berkas' => [
                $this->syaratKtp->id_syarat => $fileKtp2,
                $this->syaratKk->id_syarat => $fileKk2,
            ],
        ];

        $response = $this->actingAs($user)->post('/pengajuan', $payload);

        $response->assertSessionHasErrors(['id_layanan']);
        $this->assertDatabaseCount('pengajuan', 1);
    }

    public function test_warga_dapat_melihat_berkas_pengajuannya_sendiri(): void
    {
        $user = $this->createWargaUser(lengkap: true);
        $service = app(PengajuanService::class);

        $fileKtp = UploadedFile::fake()->image('ktp.jpg')->size(100);
        $fileKk = UploadedFile::fake()->image('kk.jpg')->size(100);

        $pengajuan = $service->buat($user->warga, $this->layananSkck, ['keperluan' => 'Uji Berkas'], [
            $this->syaratKtp->id_syarat => $fileKtp,
            $this->syaratKk->id_syarat => $fileKk,
        ], $user);

        $berkas = $pengajuan->berkas()->first();

        $response = $this->actingAs($user)->get("/pengajuan/{$pengajuan->id_pengajuan}/berkas/{$berkas->id_berkas}");

        $response->assertOk();
    }

    public function test_warga_lain_tidak_dapat_mengakses_berkas_pengajuan_orang_lain(): void
    {
        $userOwner = $this->createWargaUser(true, '1802010101900001', '081200000001');
        $userStranger = $this->createWargaUser(true, '1802010101900002', '081200000002');
        $service = app(PengajuanService::class);

        $fileKtp = UploadedFile::fake()->image('ktp.jpg')->size(100);
        $fileKk = UploadedFile::fake()->image('kk.jpg')->size(100);

        $pengajuan = $service->buat($userOwner->warga, $this->layananSkck, ['keperluan' => 'Uji Akses'], [
            $this->syaratKtp->id_syarat => $fileKtp,
            $this->syaratKk->id_syarat => $fileKk,
        ], $userOwner);

        $berkas = $pengajuan->berkas()->first();

        // Stranger mencoba mengakses berkas milik owner
        $response = $this->actingAs($userStranger)->get("/pengajuan/{$pengajuan->id_pengajuan}/berkas/{$berkas->id_berkas}");

        $response->assertForbidden();
    }

    public function test_kasi_dan_kepala_dapat_mengakses_berkas_pengajuan(): void
    {
        $userOwner = $this->createWargaUser(true, '1802010101900001', '081200000001');
        $service = app(PengajuanService::class);

        $fileKtp = UploadedFile::fake()->image('ktp.jpg')->size(100);
        $fileKk = UploadedFile::fake()->image('kk.jpg')->size(100);

        $pengajuan = $service->buat($userOwner->warga, $this->layananSkck, ['keperluan' => 'Uji Petugas'], [
            $this->syaratKtp->id_syarat => $fileKtp,
            $this->syaratKk->id_syarat => $fileKk,
        ], $userOwner);

        $berkas = $pengajuan->berkas()->first();

        $kasi = Pengguna::create([
            'nama' => 'Petugas Kasi',
            'username' => 'kasi_test',
            'password' => bcrypt('password123'),
            'role' => 'kasi',
            'status_aktif' => true,
        ]);

        $kepala = Pengguna::create([
            'nama' => 'Kepala Desa',
            'username' => 'kepala_test',
            'password' => bcrypt('password123'),
            'role' => 'kepala',
            'status_aktif' => true,
        ]);

        // Kasi berhak mengakses berkas
        $responseKasi = $this->actingAs($kasi)->get("/pengajuan/{$pengajuan->id_pengajuan}/berkas/{$berkas->id_berkas}");
        $responseKasi->assertOk();

        // Kepala berhak mengakses berkas
        $responseKepala = $this->actingAs($kepala)->get("/pengajuan/{$pengajuan->id_pengajuan}/berkas/{$berkas->id_berkas}");
        $responseKepala->assertOk();
    }

    public function test_rollback_penuh_dan_penghapusan_berkas_fisik_bila_transaksi_gagal(): void
    {
        $user = $this->createWargaUser(lengkap: true);

        $fileKtp = UploadedFile::fake()->image('ktp.jpg')->size(100);
        $fileKk = UploadedFile::fake()->image('kk.jpg')->size(100);

        $mockBerkas = $this->createMock(BerkasService::class);
        $mockBerkas->method('simpanBerkas')
            ->willThrowException(new Exception('Simulasi kegagalan simpan berkas'));

        $service = new PengajuanService($mockBerkas);

        try {
            $service->buat($user->warga, $this->layananSkck, ['keperluan' => 'Simulasi Error'], [
                $this->syaratKtp->id_syarat => $fileKtp,
                $this->syaratKk->id_syarat => $fileKk,
            ], $user);
        } catch (Exception $e) {
            $this->assertEquals('Simulasi kegagalan simpan berkas', $e->getMessage());
        }

        // Pastikan tidak ada baris pengajuan atau berkas yang tertinggal di database karena DB transaction rollback
        $this->assertDatabaseCount('pengajuan', 0);
        $this->assertDatabaseCount('berkas_pengajuan', 0);
    }

    public function test_warga_dapat_membatalkan_pengajuan_pada_status_menunggu(): void
    {
        $user = $this->createWargaUser(lengkap: true);

        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261021-0001',
            'id_warga' => $user->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'status' => 'menunggu',
            'tgl_pengajuan' => now()->toDateString(),
            'data_isian' => ['keperluan' => 'Pekerjaan'],
        ]);

        $response = $this->actingAs($user)->post("/pengajuan/{$pengajuan->id_pengajuan}/batalkan", [
            'alasan' => 'Ada kesalahan penulisan keperluan',
        ]);

        $response->assertRedirect(route('riwayat'));
        $response->assertSessionHas('success');

        $pengajuan->refresh();
        $this->assertEquals('ditolak', $pengajuan->status);
        $this->assertEquals($user->id_pengguna, $pengajuan->id_pengubah);
        $this->assertStringContainsString('Dibatalkan oleh pemohon: Ada kesalahan penulisan keperluan', $pengajuan->catatan_petugas);

        // Setelah dibatalkan (status ditolak/final), warga tidak lagi memiliki pengajuan aktif untuk layanan tersebut
        $pengajuanService = app(PengajuanService::class);
        $this->assertFalse($pengajuanService->memilikiPengajuanAktif($user->warga->id_warga, $this->layananSkck->id_layanan));
    }

    public function test_warga_tidak_dapat_membatalkan_pengajuan_yang_sudah_diproses(): void
    {
        $user = $this->createWargaUser(lengkap: true);

        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261021-0002',
            'id_warga' => $user->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'status' => 'diproses',
            'tgl_pengajuan' => now()->toDateString(),
            'data_isian' => ['keperluan' => 'Pekerjaan'],
        ]);

        $response = $this->actingAs($user)->post("/pengajuan/{$pengajuan->id_pengajuan}/batalkan", [
            'alasan' => 'Ingin batal',
        ]);

        $response->assertForbidden();

        $pengajuan->refresh();
        $this->assertEquals('diproses', $pengajuan->status);
    }

    public function test_warga_lain_tidak_dapat_membatalkan_pengajuan_milik_orang_lain(): void
    {
        $userA = $this->createWargaUser(lengkap: true, nik: '1802010101900001');
        $userB = $this->createWargaUser(lengkap: true, nik: '1802010101900009', noHp: '081234567899');

        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261021-0003',
            'id_warga' => $userA->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'status' => 'menunggu',
            'tgl_pengajuan' => now()->toDateString(),
            'data_isian' => ['keperluan' => 'Pekerjaan'],
        ]);

        $response = $this->actingAs($userB)->post("/pengajuan/{$pengajuan->id_pengajuan}/batalkan");

        $response->assertForbidden();

        $pengajuan->refresh();
        $this->assertEquals('menunggu', $pengajuan->status);
    }

    public function test_endpoint_status_json_mengembalikan_informasi_status_terkini(): void
    {
        $userA = $this->createWargaUser(lengkap: true, nik: '1802010101900001');
        $userB = $this->createWargaUser(lengkap: true, nik: '1802010101900009', noHp: '081234567899');

        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261021-0004',
            'id_warga' => $userA->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'status' => 'menunggu',
            'tgl_pengajuan' => now()->toDateString(),
            'data_isian' => ['keperluan' => 'Pekerjaan'],
        ]);

        // Akses oleh pemohon
        $response = $this->actingAs($userA)->getJson("/pengajuan/{$pengajuan->id_pengajuan}/status");

        $response->assertOk()
            ->assertJson([
                'id_pengajuan' => $pengajuan->id_pengajuan,
                'no_pengajuan' => 'PG-20261021-0004',
                'status' => 'menunggu',
                'status_label' => 'Menunggu Verifikasi',
                'is_final' => false,
            ]);

        // Akses oleh warga lain ditolak (403)
        $responseB = $this->actingAs($userB)->getJson("/pengajuan/{$pengajuan->id_pengajuan}/status");
        $responseB->assertForbidden();
    }
}
