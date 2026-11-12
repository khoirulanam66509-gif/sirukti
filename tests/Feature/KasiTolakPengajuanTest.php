<?php

namespace Tests\Feature;

use App\Models\Dusun;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\Warga;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KasiTolakPengajuanTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $kasi;

    protected Pengguna $wargaUser;

    protected Pengguna $kepala;

    protected Warga $warga;

    protected Layanan $layanan;

    protected function setUp(): void
    {
        parent::setUp();

        $dusun = Dusun::firstOrCreate(['nama_dusun' => 'Dusun I']);

        $this->kasi = Pengguna::create([
            'nama' => 'Petugas Kasi Test',
            'username' => 'kasi_tolak_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kasi',
            'status_aktif' => true,
        ]);

        $this->kepala = Pengguna::create([
            'nama' => 'Kepala Desa Test',
            'username' => 'kepala_tolak_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kepala',
            'status_aktif' => true,
        ]);

        $this->wargaUser = Pengguna::create([
            'nama' => 'Warga Pemohon Tolak',
            'username' => '180201010190'.rand(1000, 9999),
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $this->warga = Warga::create([
            'id_pengguna' => $this->wargaUser->id_pengguna,
            'id_dusun' => $dusun->id_dusun,
            'nik' => $this->wargaUser->username,
            'no_kk' => '1802010101900002',
            'tempat_lahir' => 'Lampung',
            'tanggal_lahir' => '1995-05-15',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Desa RT 001 RW 001',
            'rt' => '001',
            'rw' => '001',
        ]);

        $this->layanan = Layanan::firstOrCreate(
            ['kode' => 'SKCK'],
            ['nama_layanan' => 'Surat Pengantar SKCK', 'deskripsi' => 'Pengantar SKCK', 'status_aktif' => true, 'urutan' => 1]
        );
    }

    public function test_kasi_dapat_menolak_pengajuan_dengan_alasan_dan_mencatat_status_log(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-7701',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $alasan = 'Berkas identitas tidak valid dan pemohon bukan warga domisili tetap.';

        $response = $this->actingAs($this->kasi)
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'tolak',
                'catatan_petugas' => $alasan,
            ]);

        $response->assertRedirect(route('kasi.pengajuan.show', $pengajuan));
        $response->assertSessionHas('status', "Permohonan {$pengajuan->no_pengajuan} telah ditolak.");

        $pengajuanSegar = $pengajuan->fresh();
        $this->assertEquals('ditolak', $pengajuanSegar->status);
        $this->assertEquals($alasan, $pengajuanSegar->catatan_petugas);
        $this->assertEquals($this->kasi->id_pengguna, $pengajuanSegar->id_pengubah);
        $this->assertEquals($this->kasi->id_pengguna, $pengajuanSegar->id_verifikator);

        // Verifikasi trigger mencatat status_log baru
        $this->assertDatabaseHas('status_log', [
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'status_lama' => 'menunggu',
            'status_baru' => 'ditolak',
            'id_pengguna' => $this->kasi->id_pengguna,
            'catatan' => $alasan,
        ]);
    }

    public function test_alasan_kosong_atau_spasi_ditolak_validasi(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-7702',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        // 1. Alasan string kosong
        $responseKosong = $this->actingAs($this->kasi)
            ->from(route('kasi.pengajuan.show', $pengajuan))
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'tolak',
                'catatan_petugas' => '',
            ]);
        $responseKosong->assertSessionHasErrors('catatan_petugas');
        $this->assertEquals('menunggu', $pengajuan->fresh()->status);

        // 2. Alasan hanya berisi spasi whitespace
        $responseSpasi = $this->actingAs($this->kasi)
            ->from(route('kasi.pengajuan.show', $pengajuan))
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'tolak',
                'catatan_petugas' => '     ',
            ]);
        $responseSpasi->assertSessionHasErrors('catatan_petugas');
        $this->assertEquals('menunggu', $pengajuan->fresh()->status);
    }

    public function test_warga_dan_kepala_mendapat_403_saat_mencoba_menolak(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-7703',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        // Warga ditolak 403
        $responseWarga = $this->actingAs($this->wargaUser)
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'tolak',
                'catatan_petugas' => 'Coba tolak sendiri',
            ]);
        $responseWarga->assertStatus(403);

        // Kepala ditolak 403 (otorisasi Kasi)
        $responseKepala = $this->actingAs($this->kepala)
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'tolak',
                'catatan_petugas' => 'Coba tolak dari kepala',
            ]);
        $responseKepala->assertStatus(403);
    }

    public function test_menolak_pengajuan_berstatus_final_menghasilkan_pesan_ramah_bukan_500(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-7704',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'selesai',
            'tgl_selesai' => now(),
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->kasi->id_pengguna,
        ]);

        $response = $this->actingAs($this->kasi)
            ->from(route('kasi.pengajuan.show', $pengajuan))
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'tolak',
                'catatan_petugas' => 'Coba tolak pengajuan selesai',
            ]);

        // Harus dialihkan kembali dengan error validasi, BUKAN error 500
        $response->assertRedirect(route('kasi.pengajuan.show', $pengajuan));
        $response->assertSessionHasErrors('status');
        $this->assertEquals('selesai', $pengajuan->fresh()->status);
    }

    public function test_tampilan_modal_dialog_tolak_tajam_dan_tidak_terhalang_overlay(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-7705',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $response = $this->actingAs($this->kasi)
            ->get(route('kasi.pengajuan.show', $pengajuan));

        $response->assertStatus(200);
        // Memastikan teleportasi ke body untuk mencegah stacking context isolation
        $response->assertSee('x-teleport="body"', false);
        // Memastikan container modal dinaikkan di atas backdrop (relative z-10)
        $response->assertSee('relative z-10 min-h-full flex items-end sm:items-center justify-center', false);
        // Memastikan backdrop memiliki blur pada background saja
        $response->assertSee('fixed inset-0 bg-ink/60 backdrop-blur-sm', false);
        // Memastikan tombol modal memenuhi target sentuh minimum 44px
        $response->assertSee('min-h-[44px]', false);
        // Memastikan penutupan via escape
        $response->assertSee('@keydown.escape.window', false);
    }

    public function test_warga_melihat_status_ditolak_beserta_alasan_pada_timeline(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-7706',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Melamar Kerja'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $alasanTolak = 'Data domisili pemohon tidak cocok dengan data KTP.';

        $this->actingAs($this->kasi)
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'tolak',
                'catatan_petugas' => $alasanTolak,
            ]);

        // Warga membuka detail pengajuannya
        $responseWarga = $this->actingAs($this->wargaUser)
            ->get(route('pengajuan.show', $pengajuan));

        $responseWarga->assertStatus(200);
        $responseWarga->assertSee('Ditolak');
        $responseWarga->assertSee($alasanTolak);
    }
}
