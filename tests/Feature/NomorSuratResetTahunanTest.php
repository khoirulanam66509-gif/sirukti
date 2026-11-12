<?php

namespace Tests\Feature;

use App\Models\Dusun;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\Surat;
use App\Models\Warga;
use App\Services\SuratService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NomorSuratResetTahunanTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $kepala;

    protected Pengguna $wargaUser;

    protected Warga $warga;

    protected Layanan $layananSkck;

    protected Layanan $layananSktm;

    protected SuratService $suratService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');

        $this->suratService = app(SuratService::class);
        $dusun = Dusun::firstOrCreate(['nama_dusun' => 'Dusun I Test R5']);

        // Akun Kepala Desa dengan tanda tangan digital
        $fakeTtd = UploadedFile::fake()->image('ttd_kepala.png', 200, 100);
        $ttdPath = $fakeTtd->store('ttd', 'private');

        $this->kepala = Pengguna::create([
            'nama' => 'Kepala Desa Test R5',
            'username' => 'kepala_r5_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kepala',
            'status_aktif' => true,
            'file_ttd' => $ttdPath,
        ]);

        // Akun Warga
        $this->wargaUser = Pengguna::create([
            'nama' => 'Warga Test R5',
            'username' => '1802019999'.rand(100000, 999999),
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $this->warga = Warga::create([
            'id_pengguna' => $this->wargaUser->id_pengguna,
            'nik' => $this->wargaUser->username,
            'no_kk' => '1802019999000001',
            'id_dusun' => $dusun->id_dusun,
            'rt' => 1,
            'rw' => 1,
            'alamat' => 'Jl. Pengujian No. 5',
            'tempat_lahir' => 'Lampung',
            'tanggal_lahir' => '1991-01-01',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Petani',
        ]);

        $this->layananSkck = Layanan::firstOrCreate(
            ['kode' => 'SKCK'],
            ['nama_layanan' => 'Surat Pengantar SKCK', 'deskripsi' => 'Pengantar SKCK', 'status_aktif' => true, 'urutan' => 1]
        );

        $this->layananSktm = Layanan::firstOrCreate(
            ['kode' => 'SKTM'],
            ['nama_layanan' => 'Surat Keterangan Tidak Mampu', 'deskripsi' => 'Keterangan Tidak Mampu', 'status_aktif' => true, 'urutan' => 2]
        );
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Reset manipulasi waktu
        parent::tearDown();
    }

    /**
     * Uji: Nomor surat reset ke 001 saat berganti tahun (31 Des 23:59 WIB -> 1 Jan 00:00 WIB).
     */
    public function test_nomor_surat_otomatis_reset_ke_1_saat_berganti_tahun(): void
    {
        // 1. Simulasikan tanggal 31 Desember 2026 pukul 23:59:50 WIB
        Carbon::setTestNow(Carbon::create(2026, 12, 31, 23, 59, 50, 'Asia/Jakarta'));

        $pengajuan2026_1 = Pengajuan::create([
            'no_pengajuan' => 'PG-20261231-0001',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['keperluan' => 'Surat Akhir Tahun 1'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $pengajuan2026_2 = Pengajuan::create([
            'no_pengajuan' => 'PG-20261231-0002',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['keperluan' => 'Surat Akhir Tahun 2'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $surat2026_1 = $this->suratService->setujui($pengajuan2026_1, $this->kepala);
        $surat2026_2 = $this->suratService->setujui($pengajuan2026_2, $this->kepala);

        // Pastikan nomor surat tahun 2026 berakhiran /XII/2026
        $this->assertStringContainsString('/XII/2026', $surat2026_1->no_surat);
        $this->assertStringContainsString('/XII/2026', $surat2026_2->no_surat);

        // Ambil nomor urut surat terakhir di tahun 2026
        preg_match('/^(\d+)\//', $surat2026_2->no_surat, $matches2026);
        $nomorAkhir2026 = (int) ($matches2026[1] ?? 0);
        $this->assertGreaterThanOrEqual(2, $nomorAkhir2026);

        // 2. Transisi ke tahun baru: 1 Januari 2027 pukul 00:00:05 WIB
        Carbon::setTestNow(Carbon::create(2027, 1, 1, 0, 0, 5, 'Asia/Jakarta'));

        $pengajuan2027_1 = Pengajuan::create([
            'no_pengajuan' => 'PG-20270101-0001',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['keperluan' => 'Surat Awal Tahun Baru'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $surat2027_1 = $this->suratService->setujui($pengajuan2027_1, $this->kepala);

        // Nomor surat tahun 2027 WAJIB reset ke 001!
        $this->assertStringStartsWith('001/', $surat2027_1->no_surat);
        $this->assertStringContainsString('/I/2027', $surat2027_1->no_surat);

        // Pastikan nomor surat lama di 2026 tetap ada dan tidak berubah
        $surat2026_1Segar = Surat::find($surat2026_1->id_surat);
        $this->assertEquals($surat2026_1->no_surat, $surat2026_1Segar->no_surat);

        // 3. Tambah surat kedua di tahun 2027
        $pengajuan2027_2 = Pengajuan::create([
            'no_pengajuan' => 'PG-20270101-0002',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['keperluan' => 'Surat Awal Tahun Kedua'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $surat2027_2 = $this->suratService->setujui($pengajuan2027_2, $this->kepala);
        $this->assertStringStartsWith('002/', $surat2027_2->no_surat);
        $this->assertStringContainsString('/I/2027', $surat2027_2->no_surat);
    }

    /**
     * Uji: Nomor surat reset juga independen per jenis layanan di tahun yang sama.
     */
    public function test_nomor_surat_independen_per_layanan(): void
    {
        Carbon::setTestNow(Carbon::create(2028, 5, 10, 10, 0, 0, 'Asia/Jakarta'));

        // SKCK pertama di tahun 2028
        $pengajuanSkck = Pengajuan::create([
            'no_pengajuan' => 'PG-20280510-0001',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['keperluan' => 'SKCK Baru'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        // SKTM pertama di tahun 2028
        $pengajuanSktm = Pengajuan::create([
            'no_pengajuan' => 'PG-20280510-0002',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSktm->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['tujuan' => 'SKTM Beasiswa'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $suratSkck = $this->suratService->setujui($pengajuanSkck, $this->kepala);
        $suratSktm = $this->suratService->setujui($pengajuanSktm, $this->kepala);

        $this->assertStringStartsWith('001/', $suratSkck->no_surat);
        $this->assertStringContainsString('/SKCK/V/2028', $suratSkck->no_surat);

        $this->assertStringStartsWith('001/', $suratSktm->no_surat);
        $this->assertStringContainsString('/SKTM/V/2028', $suratSktm->no_surat);
    }
}
