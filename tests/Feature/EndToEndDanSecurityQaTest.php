<?php

namespace Tests\Feature;

use App\Models\BerkasPengajuan;
use App\Models\Dusun;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\Surat;
use App\Models\SyaratLayanan;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EndToEndDanSecurityQaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('private');
    }

    /**
     * Skenario 1: Happy Path End-to-End
     * Registrasi Warga -> Lengkapi Profil -> Ajukan Surat -> Verifikasi Kasi -> TTD Kepala -> Unduh & Verifikasi Publik
     */
    public function test_skenario_e2e_alur_lengkap_surat_terbit_dan_terverifikasi(): void
    {
        // 1. Registrasi Warga Baru
        $regData = [
            'nama' => 'Budi Santoso',
            'nik' => '1802011212950001',
            'no_hp' => '081299887766',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $regResponse = $this->post('/register', $regData);
        $regResponse->assertRedirect('/dashboard');

        $userWarga = Pengguna::where('username', '1802011212950001')->first();
        $this->assertNotNull($userWarga);
        $warga = $userWarga->warga;
        $this->assertNotNull($warga);

        // 2. Lengkapi Profil Warga
        $dusun = Dusun::first();
        $profilData = [
            'nama' => 'Budi Santoso',
            'no_kk' => '1802010101900001',
            'tempat_lahir' => 'Lampung Tengah',
            'tgl_lahir' => '1995-12-12',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Petani/Pekebun',
            'alamat' => 'Jl. Merak No. 12',
            'id_dusun' => $dusun->id_dusun,
            'rt' => '002',
            'rw' => '001',
            'no_hp' => '081299887766',
        ];

        $profilResponse = $this->actingAs($userWarga)->patch('/profil', $profilData);
        $profilResponse->assertRedirect('/profil');
        $this->assertTrue($warga->fresh()->profilLengkap());

        // 3. Warga Mengajukan Surat (SKCK)
        $layananSkck = Layanan::where('kode', 'SKCK')->first();
        $syaratList = SyaratLayanan::where('id_layanan', $layananSkck->id_layanan)->where('status_aktif', true)->get();

        $berkasFiles = [];
        foreach ($syaratList as $syarat) {
            $berkasFiles[$syarat->id_syarat] = UploadedFile::fake()->create('dokumen_'.$syarat->id_syarat.'.pdf', 200, 'application/pdf');
        }

        $pengajuanPayload = [
            'id_layanan' => $layananSkck->id_layanan,
            'data_isian' => [
                'keperluan' => 'Melamar pekerjaan di instansi pemerintah',
            ],
            'berkas' => $berkasFiles,
        ];

        $ajukanResponse = $this->actingAs($userWarga)->post('/pengajuan', $pengajuanPayload);
        $ajukanResponse->assertRedirect('/riwayat');

        $pengajuan = Pengajuan::where('id_warga', $warga->id_warga)->latest('id_pengajuan')->first();
        $this->assertNotNull($pengajuan);
        $this->assertEquals('menunggu', $pengajuan->status);

        // 4. Kasi Memverifikasi Berkas Pengajuan dan Memproses
        $kasi = Pengguna::where('role', 'kasi')->first();

        // Step 4a: Kasi verifikasi berkas menjadi 'sesuai' dan ubah status ke 'diproses'
        $verifikasiPayload = [
            'aksi' => 'proses',
            'catatan_petugas' => 'Seluruh berkas lengkap dan sesuai.',
            'berkas' => [],
        ];
        foreach ($pengajuan->berkas as $berkas) {
            $verifikasiPayload['berkas'][$berkas->id_berkas] = [
                'status_verifikasi' => 'sesuai',
                'catatan' => 'Sesuai dan valid',
            ];
        }

        $verifResponse = $this->actingAs($kasi)->post("/kasi/antrean/{$pengajuan->id_pengajuan}/verifikasi", $verifikasiPayload);
        $verifResponse->assertRedirect(route('kasi.pengajuan.show', $pengajuan));
        $this->assertEquals('diproses', $pengajuan->fresh()->status);

        // Step 4b: Kasi mengajukan draf surat ke Kepala Kampung (status 'menunggu_ttd')
        $ajukanTtdResponse = $this->actingAs($kasi)->post("/kasi/antrean/{$pengajuan->id_pengajuan}/verifikasi", [
            'aksi' => 'ajukan_ttd',
            'catatan_petugas' => 'Draf surat siap ditandatangani oleh Kepala Kampung.',
        ]);
        $ajukanTtdResponse->assertRedirect(route('kasi.pengajuan.show', $pengajuan));
        $this->assertEquals('menunggu_ttd', $pengajuan->fresh()->status);

        // 5. Kepala Kampung Menyetujui dan Menandatangani Surat
        $kepala = Pengguna::where('role', 'kepala')->first();

        // Pastikan file fisik tanda tangan tersimpan di disk private yang di-fake
        $fileTtd = UploadedFile::fake()->image('ttd_asli.png', 300, 150);
        $pathTtd = $fileTtd->store('ttd', 'private');
        $kepala->update(['file_ttd' => $pathTtd]);

        $setujuiResponse = $this->actingAs($kepala)->post("/kepala/persetujuan/{$pengajuan->id_pengajuan}/setujui");
        $setujuiResponse->assertRedirect(route('kepala.persetujuan.index'));

        $pengajuan->refresh();
        $this->assertEquals('selesai', $pengajuan->status);
        $this->assertNotNull($pengajuan->surat);
        $this->assertNotNull($pengajuan->surat->no_surat);
        $this->assertNotNull($pengajuan->surat->kode_verifikasi);

        // 6. Warga Mengunduh Surat Resmi PDF
        $unduhResponse = $this->actingAs($userWarga)->get("/pengajuan/{$pengajuan->id_pengajuan}/unduh-surat");
        $unduhResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $unduhResponse->headers->get('content-type'));

        // 7. Pihak Ketiga Memverifikasi Keaslian Surat via URL Publik
        $kodeVerifikasi = $pengajuan->surat->kode_verifikasi;
        $verifPublikResponse = $this->get("/verifikasi/{$kodeVerifikasi}");
        $verifPublikResponse->assertStatus(200);
        $verifPublikResponse->assertSee('Surat Terverifikasi');
        $verifPublikResponse->assertSee($pengajuan->surat->no_surat);
        // NIK harus tersamarkan (tidak bocor 16 digit utuh)
        $verifPublikResponse->assertDontSee('1802011212950001');
    }

    /**
     * Skenario 2: Jalur Kurang Berkas & Pengunggahan Ulang
     */
    public function test_skenario_kurang_berkas_dan_unggah_ulang(): void
    {
        $wargaUser = Pengguna::where('role', 'warga')->first();
        $warga = $wargaUser->warga;
        $layanan = Layanan::first();

        // Buat pengajuan dummy
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-TEST-0001',
            'id_warga' => $warga->id_warga,
            'id_layanan' => $layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Uji coba'],
        ]);

        $syarat = SyaratLayanan::where('id_layanan', $layanan->id_layanan)->first();
        $berkas = BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $syarat->id_syarat,
            'nama_file_asli' => 'ktp.jpg',
            'path_file' => 'berkas/test_ktp.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 120,
            'status_verifikasi' => 'menunggu',
        ]);

        // Kasi menetapkan berkas perlu perbaikan
        $kasi = Pengguna::where('role', 'kasi')->first();
        $payloadKasi = [
            'aksi' => 'minta_perbaikan',
            'catatan_petugas' => 'Foto KTP buram dan tidak terbaca.',
            'berkas' => [
                $berkas->id_berkas => [
                    'status_verifikasi' => 'perlu_perbaikan',
                    'catatan' => 'Silakan foto ulang KTP dengan pencahayaan terang.',
                ],
            ],
        ];

        $resKasi = $this->actingAs($kasi)->post("/kasi/antrean/{$pengajuan->id_pengajuan}/verifikasi", $payloadKasi);
        $resKasi->assertRedirect();
        $this->assertEquals('kurang_berkas', $pengajuan->fresh()->status);

        // Warga mengunggah ulang berkas
        $newFile = UploadedFile::fake()->create('ktp_terang.jpg', 300, 'image/jpeg');
        $resUnggahUlang = $this->actingAs($wargaUser)->post("/pengajuan/{$pengajuan->id_pengajuan}/unggah-ulang", [
            'berkas' => [
                $berkas->id_berkas => $newFile,
            ],
        ]);

        $resUnggahUlang->assertRedirect();
        $berkas->refresh();
        $this->assertEquals('menunggu', $berkas->status_verifikasi);
        $this->assertEquals('menunggu', $pengajuan->fresh()->status);
    }

    /**
     * Skenario 3: Keamanan & Hak Akses Antar-Pengguna
     */
    public function test_keamanan_isolasi_data_antar_warga(): void
    {
        $wargaUsers = Pengguna::where('role', 'warga')->take(2)->get();
        if ($wargaUsers->count() < 2) {
            $this->markTestSkipped('Perlu minimal 2 user warga di seeder untuk uji isolasi.');
        }

        $wargaA = $wargaUsers[0];
        $wargaB = $wargaUsers[1];

        // Buat pengajuan milik warga A
        $pengajuanA = Pengajuan::where('id_warga', $wargaA->warga->id_warga)->first();
        if (! $pengajuanA) {
            $pengajuanA = Pengajuan::create([
                'no_pengajuan' => 'PG-A-001',
                'id_warga' => $wargaA->warga->id_warga,
                'id_layanan' => Layanan::first()->id_layanan,
                'tgl_pengajuan' => now(),
                'status' => 'diproses',
                'data_isian' => [],
            ]);
        }

        // Warga B mencoba melihat detail pengajuan warga A -> Dilarang 403
        $res = $this->actingAs($wargaB)->get("/pengajuan/{$pengajuanA->id_pengajuan}");
        $res->assertStatus(403);

        // Warga B mencoba mengunduh surat warga A -> Dilarang 403
        $resUnduh = $this->actingAs($wargaB)->get("/pengajuan/{$pengajuanA->id_pengajuan}/unduh-surat");
        $resUnduh->assertStatus(403);
    }

    /**
     * Skenario 4: Keamanan Rute Antar-Role
     */
    public function test_keamanan_rute_antar_role(): void
    {
        $wargaUser = Pengguna::where('role', 'warga')->first();
        $kasiUser = Pengguna::where('role', 'kasi')->first();

        // Warga dilarang mengakses modul Kasi
        $this->actingAs($wargaUser)->get('/kasi/antrean')->assertStatus(403);
        $this->actingAs($wargaUser)->get('/kasi/warga')->assertStatus(403);
        $this->actingAs($wargaUser)->get('/kasi/pengaturan')->assertStatus(403);

        // Warga dilarang mengakses modul Kepala
        $this->actingAs($wargaUser)->get('/kepala/persetujuan')->assertStatus(403);
        $this->actingAs($wargaUser)->get('/kepala/profil-ttd')->assertStatus(403);

        // Kasi dilarang mengakses persetujuan Kepala
        $this->actingAs($kasiUser)->get('/kepala/persetujuan')->assertStatus(403);
        $this->actingAs($kasiUser)->get('/kepala/profil-ttd')->assertStatus(403);
    }
}
