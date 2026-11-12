<?php

namespace Tests\Feature\Kasi;

use App\Models\Dusun;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\Warga;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PencarianAjaxKasiTest extends TestCase
{
    use DatabaseTransactions;

    protected Dusun $dusun;

    protected Layanan $layananSkck;

    protected Layanan $layananSktm;

    protected Pengguna $kasi;

    protected Pengguna $userWarga1;

    protected Warga $warga1;

    protected Pengguna $userWarga2;

    protected Warga $warga2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dusun = Dusun::firstOrCreate(['nama_dusun' => 'Dusun I Test Ajax Kasi']);

        $this->layananSkck = Layanan::firstOrCreate(
            ['kode' => 'SKCK'],
            ['nama_layanan' => 'Surat Pengantar SKCK', 'deskripsi' => 'Pengantar SKCK', 'status_aktif' => true, 'urutan' => 1]
        );

        $this->layananSktm = Layanan::firstOrCreate(
            ['kode' => 'SKTM'],
            ['nama_layanan' => 'Surat Keterangan Tidak Mampu', 'deskripsi' => 'Keterangan Tidak Mampu', 'status_aktif' => true, 'urutan' => 2]
        );

        $this->kasi = Pengguna::create([
            'nama' => 'Petugas Kasi Test Ajax',
            'username' => 'kasi_ajax_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kasi',
            'status_aktif' => true,
        ]);

        // Warga 1
        $nik1 = '1802011111'.rand(100000, 999999);
        $this->userWarga1 = Pengguna::create([
            'nama' => 'Bambang Pamungkas',
            'username' => 'bambang_top',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
            'no_hp' => '081234567890',
        ]);

        $this->warga1 = Warga::create([
            'id_pengguna' => $this->userWarga1->id_pengguna,
            'nik' => $nik1,
            'no_kk' => '1802011111000001',
            'id_dusun' => $this->dusun->id_dusun,
            'rt' => 1,
            'rw' => 1,
            'alamat' => 'Jl. Bambang No. 10',
            'tempat_lahir' => 'Lampung',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Atlet',
        ]);

        // Warga 2
        $nik2 = '1802012222'.rand(100000, 999999);
        $this->userWarga2 = Pengguna::create([
            'nama' => 'Sri Mulyani',
            'username' => 'sri_mul',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
            'no_hp' => '089876543210',
        ]);

        $this->warga2 = Warga::create([
            'id_pengguna' => $this->userWarga2->id_pengguna,
            'nik' => $nik2,
            'no_kk' => '1802012222000002',
            'id_dusun' => $this->dusun->id_dusun,
            'rt' => 2,
            'rw' => 1,
            'alamat' => 'Jl. Sri No. 20',
            'tempat_lahir' => 'Lampung',
            'tanggal_lahir' => '1992-02-02',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'PNS',
        ]);
    }

    /**
     * Uji: Halaman antrean Kasi mengembalikan full view pada request biasa dan partial view pada request AJAX.
     */
    public function test_antrean_kasi_halaman_penuh_dan_ajax_partial(): void
    {
        // 1. Request biasa -> full view
        $fullResponse = $this->actingAs($this->kasi)->get(route('kasi.pengajuan.index'));
        $fullResponse->assertStatus(200);
        $fullResponse->assertSee('Antrean Verifikasi Surat');
        $fullResponse->assertSee('id="area-hasil-antrean"', false);

        // 2. Request AJAX -> partial view
        $ajaxResponse = $this->actingAs($this->kasi)->get(route('kasi.pengajuan.index'), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertSee('id="kontainer-antrean"', false);
        $ajaxResponse->assertDontSee('<!DOCTYPE html>', false);
        $ajaxResponse->assertDontSee('Modul Aparatur Desa');
    }

    /**
     * Uji: Pencarian AJAX pada antrean Kasi berdasarkan no_pengajuan, nama pemohon, dan NIK.
     */
    public function test_antrean_kasi_pencarian_ajax(): void
    {
        $pengajuan1 = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-7701',
            'id_warga' => $this->warga1->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'tgl_pengajuan' => now()->subMinutes(10),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->userWarga1->id_pengguna,
        ]);

        $pengajuan2 = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-7702',
            'id_warga' => $this->warga2->id_warga,
            'id_layanan' => $this->layananSktm->id_layanan,
            'tgl_pengajuan' => now()->subMinutes(5),
            'status' => 'diproses',
            'data_isian' => ['tujuan' => 'Bantuan'],
            'id_pengubah' => $this->userWarga2->id_pengguna,
        ]);

        // Cari no_pengajuan
        $respNo = $this->actingAs($this->kasi)->get(route('kasi.pengajuan.index', ['cari' => '7701']), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $respNo->assertSee($pengajuan1->no_pengajuan);
        $respNo->assertDontSee($pengajuan2->no_pengajuan);

        // Cari nama warga
        $respNama = $this->actingAs($this->kasi)->get(route('kasi.pengajuan.index', ['cari' => 'Bambang']), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $respNama->assertSee('Bambang Pamungkas');
        $respNama->assertDontSee('Sri Mulyani');

        // Cari NIK
        $respNik = $this->actingAs($this->kasi)->get(route('kasi.pengajuan.index', ['cari' => substr($this->warga2->nik, -4)]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $respNik->assertSee($pengajuan2->no_pengajuan);
        $respNik->assertDontSee($pengajuan1->no_pengajuan);
    }

    /**
     * Uji: Kelola Warga mengembalikan full view pada request biasa dan partial view pada request AJAX.
     */
    public function test_kelola_warga_halaman_penuh_dan_ajax_partial(): void
    {
        // 1. Request biasa -> full view
        $fullResponse = $this->actingAs($this->kasi)->get(route('kasi.warga.index'));
        $fullResponse->assertStatus(200);
        $fullResponse->assertSee('Kelola Data Warga');
        $fullResponse->assertSee('id="area-hasil-warga"', false);

        // 2. Request AJAX -> partial view
        $ajaxResponse = $this->actingAs($this->kasi)->get(route('kasi.warga.index'), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertSee('id="kontainer-warga"', false);
        $ajaxResponse->assertDontSee('<!DOCTYPE html>', false);
        $ajaxResponse->assertDontSee('Kewenangan Kasi Pelayanan');
    }

    /**
     * Uji: Pencarian AJAX pada Kelola Warga berdasarkan NIK, nama, dan username.
     */
    public function test_kelola_warga_pencarian_ajax(): void
    {
        // Cari nama
        $respNama = $this->actingAs($this->kasi)->get(route('kasi.warga.index', ['q' => 'Bambang']), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $respNama->assertSee('Bambang Pamungkas');
        $respNama->assertDontSee('Sri Mulyani');

        // Cari NIK
        $respNik = $this->actingAs($this->kasi)->get(route('kasi.warga.index', ['q' => substr($this->warga2->nik, -5)]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $respNik->assertSee('Sri Mulyani');
        $respNik->assertDontSee('Bambang Pamungkas');

        // Cari Username
        $respUsername = $this->actingAs($this->kasi)->get(route('kasi.warga.index', ['q' => 'bambang_top']), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $respUsername->assertSee('Bambang Pamungkas');
        $respUsername->assertDontSee('Sri Mulyani');
    }

    /**
     * Uji: R3 - Kolom Nomor WhatsApp di Kelola Warga menampilkan ikon hijau WhatsApp (#25D366)
     * baik pada tampilan desktop maupun kartu mobile.
     */
    public function test_kelola_warga_ikon_whatsapp_berwarna_hijau_r3(): void
    {
        $response = $this->actingAs($this->kasi)->get(route('kasi.warga.index'));
        $response->assertStatus(200);

        // Pastikan class warna teks hijau WhatsApp hadir pada SVG ikon WhatsApp
        $response->assertSee('text-[#25D366]', false);

        // Pastikan tidak ada ikon WhatsApp yang masih menggunakan class text-primary
        // (Sebelumnya: <svg class="w-4 h-4 text-primary shrink-0" fill="currentColor" viewBox="0 0 24 24"> pada td WhatsApp)
        $response->assertDontSee('<svg class="w-4 h-4 text-primary shrink-0" fill="currentColor" viewBox="0 0 24 24">', false);
    }

    /**
     * Uji: Akses antrean verifikasi dan kelola warga oleh selain Kasi ditolak (403).
     */
    public function test_proteksi_role_kasi(): void
    {
        $responseWargaAntrean = $this->actingAs($this->userWarga1)->get(route('kasi.pengajuan.index'));
        $responseWargaAntrean->assertStatus(403);

        $responseWargaKelola = $this->actingAs($this->userWarga1)->get(route('kasi.warga.index'));
        $responseWargaKelola->assertStatus(403);
    }
}
