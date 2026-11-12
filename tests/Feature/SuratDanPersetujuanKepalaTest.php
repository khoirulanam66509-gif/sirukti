<?php

namespace Tests\Feature;

use App\Models\Dusun;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengaturan;
use App\Models\Pengguna;
use App\Models\Warga;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuratDanPersetujuanKepalaTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $kasi;

    protected Pengguna $kepala;

    protected Pengguna $wargaUser;

    protected Warga $warga;

    protected Layanan $layanan;

    protected function setUp(): void
    {
        parent::setUp();

        $dusun = Dusun::firstOrCreate(['nama_dusun' => 'Dusun I']);

        $this->kasi = Pengguna::create([
            'nama' => 'Kasi Pelayanan Test',
            'username' => 'kasi_srt_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kasi',
            'status_aktif' => true,
        ]);

        $this->kepala = Pengguna::create([
            'nama' => 'Kepala Desa Test',
            'username' => 'kepala_srt_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kepala',
            'jabatan' => 'Kepala Kampung Rukti Harjo',
            'status_aktif' => true,
        ]);

        $this->wargaUser = Pengguna::create([
            'nama' => 'Warga Pemohon Surat',
            'username' => '180201010190'.rand(1000, 9999),
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $this->warga = Warga::create([
            'id_pengguna' => $this->wargaUser->id_pengguna,
            'nik' => $this->wargaUser->username,
            'no_kk' => '1802010101900001',
            'id_dusun' => $dusun->id_dusun,
            'rt' => 1,
            'rw' => 1,
            'alamat' => 'Jl. Kenanga No. 5',
            'tempat_lahir' => 'Lampung',
            'tanggal_lahir' => '1998-08-17',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'belum_kawin',
            'pekerjaan' => 'Karyawan Swasta',
        ]);

        $this->layanan = Layanan::firstOrCreate(
            ['kode' => 'SKCK'],
            ['nama_layanan' => 'Surat Pengantar SKCK', 'deskripsi' => 'Pengantar SKCK', 'status_aktif' => true, 'urutan' => 1]
        );

        // Pastikan pengaturan profil kampung dan format surat terisi
        Pengaturan::simpan('profil_kampung', [
            'nama_kampung' => 'Rukti Harjo',
            'kecamatan' => 'Seputih Raman',
            'kabupaten' => 'Lampung Tengah',
            'alamat' => 'Jl. Merdeka No. 1',
            'kode_pos' => '34155',
        ]);

        Pengaturan::simpan('surat', [
            'kode_kampung' => 'RH',
            'jabatan_penandatangan' => 'Kepala Kampung Rukti Harjo',
        ]);
    }

    public function test_kasi_dapat_melihat_pratinjau_draft_surat(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-9901',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'diproses',
            'data_isian' => ['keperluan' => 'Pemberkasan BUMN'],
            'id_pengubah' => $this->kasi->id_pengguna,
        ]);

        // Halaman pratinjau draft Blade K4
        $responseDraft = $this->actingAs($this->kasi)
            ->get(route('kasi.pengajuan.draft', $pengajuan));
        $responseDraft->assertStatus(200);
        $responseDraft->assertSee('Pratinjau Draft: Surat Pengantar SKCK');

        // Streaming PDF draft
        $responsePdf = $this->actingAs($this->kasi)
            ->get(route('kasi.pengajuan.draft.pdf', $pengajuan));
        $responsePdf->assertStatus(200);
        $this->assertEquals('application/pdf', $responsePdf->headers->get('Content-Type'));
    }

    public function test_halaman_persetujuan_hanya_dapat_diakses_oleh_kepala(): void
    {
        // 1. Guest redirect ke login
        $this->get(route('kepala.persetujuan.index'))->assertRedirect(route('login'));

        // 2. Warga mendapat 403 Forbidden
        $this->actingAs($this->wargaUser)->get(route('kepala.persetujuan.index'))->assertStatus(403);

        // 3. Kasi mendapat 403 Forbidden
        $this->actingAs($this->kasi)->get(route('kepala.persetujuan.index'))->assertStatus(403);

        // 4. Kepala dapat mengakses 200 OK
        $response = $this->actingAs($this->kepala)->get(route('kepala.persetujuan.index'));
        $response->assertStatus(200);
        $response->assertSee('Persetujuan Surat Resmi');
    }

    public function test_setujui_ditolak_jika_kepala_belum_mengunggah_tanda_tangan(): void
    {
        Storage::fake('private');

        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-9902',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['keperluan' => 'Melamar Pekerjaan'],
            'id_pengubah' => $this->kasi->id_pengguna,
        ]);

        // Kepala belum memiliki file_ttd
        $this->assertNull($this->kepala->file_ttd);

        $response = $this->actingAs($this->kepala)
            ->post(route('kepala.persetujuan.setujui', $pengajuan));

        $response->assertSessionHasErrors('file_ttd');
        $this->assertEquals('menunggu_ttd', $pengajuan->fresh()->status);
    }

    public function test_kepala_dapat_menyetujui_surat_dan_menerbitkan_surat_resmi_pdf(): void
    {
        Storage::fake('private');

        // Siapkan tanda tangan untuk Kepala
        $fileTtd = UploadedFile::fake()->image('ttd_asli.png', 300, 150);
        $pathTtd = $fileTtd->store('ttd', 'private');
        $this->kepala->update(['file_ttd' => $pathTtd]);

        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-9903',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['keperluan' => 'Pendaftaran Bintara POLRI'],
            'id_pengubah' => $this->kasi->id_pengguna,
        ]);

        $response = $this->actingAs($this->kepala)
            ->post(route('kepala.persetujuan.setujui', $pengajuan));

        $response->assertRedirect(route('kepala.persetujuan.index'));
        $response->assertSessionHas('status');

        $pengajuanSegar = $pengajuan->fresh();
        $this->assertEquals('selesai', $pengajuanSegar->status);
        $this->assertNotNull($pengajuanSegar->tgl_selesai);

        // Periksa record surat terbentuk
        $surat = $pengajuanSegar->surat;
        $this->assertNotNull($surat);
        $this->assertStringContainsString('/RH/SKCK/', $surat->no_surat);
        $this->assertEquals(32, strlen($surat->kode_verifikasi));
        $this->assertEquals($this->kepala->id_pengguna, $surat->id_penandatangan);

        // Pastikan file fisik PDF tersimpan di disk privat
        Storage::disk('private')->assertExists($surat->file_pdf);

        // Cek log riwayat status
        $this->assertDatabaseHas('status_log', [
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'status_lama' => 'menunggu_ttd',
            'status_baru' => 'selesai',
            'id_pengguna' => $this->kepala->id_pengguna,
        ]);

        // Verifikasi warga pemilik dapat mengunduh surat resmi yang sudah selesai
        $responseUnduh = $this->actingAs($this->wargaUser)
            ->get(route('surat.unduh', $pengajuan));
        $responseUnduh->assertStatus(200);
        $this->assertEquals('application/pdf', $responseUnduh->headers->get('Content-Type'));
    }

    public function test_kepala_dapat_mengembalikan_permohonan_ke_kasi_dengan_catatan(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-9904',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['keperluan' => 'Beasiswa'],
            'id_pengubah' => $this->kasi->id_pengguna,
        ]);

        // Gagal jika catatan kosong
        $responseKosong = $this->actingAs($this->kepala)
            ->post(route('kepala.persetujuan.kembalikan', $pengajuan), ['catatan' => '   ']);
        $responseKosong->assertSessionHasErrors('catatan');

        // Berhasil dengan catatan valid
        $responseValid = $this->actingAs($this->kepala)
            ->post(route('kepala.persetujuan.kembalikan', $pengajuan), [
                'catatan' => 'Mohon cek kembali RT dan tanggal lahir pemohon.',
            ]);

        $responseValid->assertRedirect(route('kepala.persetujuan.index'));

        $pengajuanSegar = $pengajuan->fresh();
        $this->assertEquals('diproses', $pengajuanSegar->status);
        $this->assertEquals('Mohon cek kembali RT dan tanggal lahir pemohon.', $pengajuanSegar->catatan_petugas);
    }

    public function test_kepala_dapat_melihat_tab_antrean_menunggu_ttd_dan_riwayat_selesai(): void
    {
        $pengajuan1 = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-9905',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['keperluan' => 'Kredit Usaha'],
            'id_pengubah' => $this->kasi->id_pengguna,
        ]);

        $pengajuan2 = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-9906',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'selesai',
            'data_isian' => ['keperluan' => 'Pindah Domisili'],
            'id_pengubah' => $this->kasi->id_pengguna,
        ]);

        // Tab menunggu TTD (default)
        $responseMenunggu = $this->actingAs($this->kepala)
            ->get(route('kepala.persetujuan.index', ['tab' => 'menunggu_ttd']));
        $responseMenunggu->assertStatus(200);
        $responseMenunggu->assertSee($pengajuan1->no_pengajuan);
        $responseMenunggu->assertDontSee($pengajuan2->no_pengajuan);

        // Tab selesai
        $responseSelesai = $this->actingAs($this->kepala)
            ->get(route('kepala.persetujuan.index', ['tab' => 'selesai']));
        $responseSelesai->assertStatus(200);
        $responseSelesai->assertSee($pengajuan2->no_pengajuan);
        $responseSelesai->assertDontSee($pengajuan1->no_pengajuan);
    }

    public function test_surat_resmi_memuat_stempel_jika_kepala_memiliki_file_stempel(): void
    {
        Storage::fake('private');

        // Siapkan tanda tangan & stempel untuk Kepala
        $fileTtd = UploadedFile::fake()->image('ttd_asli.png', 300, 150);
        $pathTtd = $fileTtd->store('ttd', 'private');

        $fileStempel = UploadedFile::fake()->image('stempel_asli.png', 300, 300);
        $pathStempel = $fileStempel->store('stempel', 'private');

        $this->kepala->update([
            'file_ttd' => $pathTtd,
            'file_stempel' => $pathStempel,
        ]);

        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-9907',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['keperluan' => 'Pendaftaran Haji'],
            'id_pengubah' => $this->kasi->id_pengguna,
        ]);

        $response = $this->actingAs($this->kepala)
            ->post(route('kepala.persetujuan.setujui', $pengajuan));

        $response->assertRedirect(route('kepala.persetujuan.index'));
        $pengajuanSegar = $pengajuan->fresh();
        $this->assertEquals('selesai', $pengajuanSegar->status);

        $surat = $pengajuanSegar->surat;
        $this->assertNotNull($surat);
        Storage::disk('private')->assertExists($surat->file_pdf);

        // Unduh PDF
        $responseUnduh = $this->actingAs($this->wargaUser)->get(route('surat.unduh', $pengajuan));
        $responseUnduh->assertStatus(200);
    }

    public function test_halaman_show_persetujuan_menampilkan_status_stempel_dan_preset(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-9908',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['keperluan' => 'Pemasangan Listrik'],
            'id_pengubah' => $this->kasi->id_pengguna,
        ]);

        $response = $this->actingAs($this->kepala)->get(route('kepala.persetujuan.show', $pengajuan));
        $response->assertStatus(200);
        $response->assertSee('Cap Stempel');
        $response->assertSee('Data belum sesuai');
        $response->assertSee('showModalKembalikan');
        $response->assertSee('showModalSetujui');
        $response->assertSee('Setujui &amp; Tandatangani Surat?', false);
        $response->assertDontSee('onsubmit="return confirm(', false);
        $response->assertSee('relative z-10 min-h-full flex items-end sm:items-center justify-center', false);
        $response->assertSee('Kembalikan Permohonan ke Kasi');
    }

    public function test_layout_surat_pdf_memiliki_spasi_paragraf_dan_posisi_stempel_ttd_yang_diperbarui(): void
    {
        $html = view('surat.templates.skck', [
            'judul_surat' => 'SURAT PENGANTAR CATATAN KEPOLISIAN',
            'no_surat' => '001/RH/SKCK/X/2026',
            'tgl_surat' => '26 Oktober 2026',
            'kop' => [
                'nama_kampung' => 'Rukti Harjo',
                'kecamatan' => 'Seputih Raman',
                'kabupaten' => 'Lampung Tengah',
                'alamat' => 'Jl. Merdeka No. 1',
                'kode_pos' => '34155',
            ],
            'surat_cfg' => [
                'kode_kampung' => 'RH',
                'jabatan_penandatangan' => 'Kepala Kampung Rukti Harjo',
            ],
            'warga' => $this->warga,
            'pengajuan' => new Pengajuan(['no_pengajuan' => 'PG-20261026-0001']),
            'layanan' => $this->layanan,
            'data_isian' => ['keperluan' => 'Melamar Pekerjaan'],
            'penandatangan' => $this->kepala,
            'ttd_base64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'stempel_base64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'qr_base64' => 'data:image/svg+xml;base64,PHN2Zz48L3N2Zz4=',
            'url_verifikasi' => url('/verifikasi/test1234'),
            'is_draft' => false,
        ])->render();

        $this->assertStringContainsString('margin: 0 0 3.5mm 0;', $html);
        $this->assertStringContainsString('left: -9mm;', $html);
        $this->assertStringContainsString('left: 2mm;', $html);
    }
}
