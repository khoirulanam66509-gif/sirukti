<?php

namespace Tests\Feature;

use App\Models\Dusun;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\Warga;
use App\Services\SuratService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuratPdfQrLayoutTest extends TestCase
{
    use DatabaseTransactions;

    protected SuratService $suratService;

    protected Pengguna $kepala;

    protected Pengguna $userWarga;

    protected Warga $warga;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');
        $this->suratService = app(SuratService::class);

        $fakeTtd = UploadedFile::fake()->image('ttd_kepala.png', 200, 100);
        $ttdPath = $fakeTtd->store('ttd', 'private');

        $this->kepala = Pengguna::create([
            'nama' => 'I Nyoman Sudarta, S.Sos.',
            'username' => 'kepala_pdf_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kepala',
            'status_aktif' => true,
            'file_ttd' => $ttdPath,
            'nip' => '19750101 200003 1 001',
        ]);

        $dusun = Dusun::firstOrCreate(['nama_dusun' => 'Dusun I Test PDF']);

        $this->userWarga = Pengguna::create([
            'nama' => 'Warga Pemohon PDF',
            'username' => '1802018888'.rand(100000, 999999),
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
            'no_hp' => '081234567899',
        ]);

        $this->warga = Warga::create([
            'id_pengguna' => $this->userWarga->id_pengguna,
            'nik' => $this->userWarga->username,
            'no_kk' => '1802018888000001',
            'id_dusun' => $dusun->id_dusun,
            'rt' => 1,
            'rw' => 1,
            'alamat' => 'Jl. Merdeka No. 10 RT 001/RW 001 Dusun I, Kampung Rukti Harjo',
            'tempat_lahir' => 'Lampung Tengah',
            'tanggal_lahir' => '1992-05-10',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Wiraswasta',
        ]);
    }

    /**
     * Uji: Layout HTML surat memuat CSS penataan QR sejajar secara horizontal dengan kolom TTD.
     */
    public function test_layout_css_qr_sejajar_horizontal_dengan_ttd(): void
    {
        $layanan = Layanan::firstOrCreate(
            ['kode' => 'SKCK'],
            ['nama_layanan' => 'Surat Pengantar SKCK', 'deskripsi' => 'SKCK', 'status_aktif' => true, 'urutan' => 1]
        );

        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261102-0001',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['keperluan' => 'Pendaftaran Pekerjaan'],
            'id_pengubah' => $this->userWarga->id_pengguna,
        ]);

        // Cek bahwa view layout memuat CSS vertical-align: top dan padding-top pada .ttd-kiri
        $viewHtml = view('surat.templates.skck', [
            'kop' => ['nama_kampung' => 'Rukti Harjo', 'kecamatan' => 'Seputih Raman', 'kabupaten' => 'Lampung Tengah', 'alamat' => 'Jl. Merdeka No. 1', 'kode_pos' => '34155'],
            'surat_cfg' => ['kode_kampung' => 'RH', 'jabatan_penandatangan' => 'Kepala Kampung Rukti Harjo'],
            'judul_surat' => 'SURAT PENGANTAR SKCK',
            'warga' => $this->warga,
            'pengajuan' => $pengajuan,
            'layanan' => $layanan,
            'data_isian' => $pengajuan->data_isian,
            'penandatangan' => $this->kepala,
            'qr_base64' => 'data:image/svg+xml;base64,PHN2Zz48L3N2Zz4=',
            'url_verifikasi' => url('/verifikasi/test1234'),
            'is_draft' => false,
        ])->render();

        $this->assertStringContainsString('.ttd-kiri', $viewHtml);
        $this->assertStringContainsString('vertical-align: top;', $viewHtml);
        $this->assertStringContainsString('padding-top: 5.5mm;', $viewHtml);
        $this->assertStringContainsString('NIP. 19750101 200003 1 001', $viewHtml);
        $this->assertStringContainsString('class="kop-logo"', $viewHtml);
        $this->assertStringContainsString('data:image/png;base64,', $viewHtml);
    }

    /**
     * Uji: Seluruh 4 jenis surat (SKCK, DOM, SKTM, SKU) tetap tepat 1 lembar A4.
     */
    public function test_seluruh_template_surat_tetap_satu_halaman_a4(): void
    {
        $daftarJenis = [
            'SKCK' => ['keperluan' => 'Melamar Pekerjaan di BUMN'],
            'DOM' => ['alamat_domisili' => 'Kampung Rukti Harjo', 'lama_tinggal_tahun' => '5', 'keperluan' => 'Pendaftaran KTP'],
            'SKTM' => ['tujuan' => 'Pengajuan Keringanan Biaya Rumah Sakit', 'jumlah_tanggungan' => '4', 'penghasilan_per_bulan' => 1500000],
            'SKU' => ['nama_usaha' => 'Toko Sembako Berkah Jaya', 'jenis_usaha' => 'Perdagangan Eceran', 'lama_usaha_tahun' => '3', 'alamat_usaha' => 'Jl. Utama RT 001/RW 001'],
        ];

        foreach ($daftarJenis as $kode => $isian) {
            $layanan = Layanan::firstOrCreate(
                ['kode' => $kode],
                ['nama_layanan' => "Surat {$kode}", 'deskripsi' => $kode, 'status_aktif' => true, 'urutan' => 1]
            );

            $pengajuan = Pengajuan::create([
                'no_pengajuan' => "PG-20261102-{$kode}",
                'id_warga' => $this->warga->id_warga,
                'id_layanan' => $layanan->id_layanan,
                'tgl_pengajuan' => now(),
                'status' => 'menunggu_ttd',
                'data_isian' => $isian,
                'id_pengubah' => $this->userWarga->id_pengguna,
            ]);

            // Render PDF draft
            $pdfDraft = $this->suratService->renderPdf($pengajuan, null, true, $this->kepala);
            $canvasDraft = $pdfDraft->getDompdf()->getCanvas();
            $pageCountDraft = $canvasDraft->get_page_count();
            $this->assertEquals(1, $pageCountDraft, "Surat draft {$kode} melebihi 1 halaman: terdeteksi {$pageCountDraft} halaman.");

            // Render PDF final dengan QR dan TTD
            $surat = $this->suratService->setujui($pengajuan, $this->kepala);
            $pdfFinal = $this->suratService->renderPdf($pengajuan, $surat, false, $this->kepala);
            $canvasFinal = $pdfFinal->getDompdf()->getCanvas();
            $pageCountFinal = $canvasFinal->get_page_count();
            $this->assertEquals(1, $pageCountFinal, "Surat final {$kode} melebihi 1 halaman: terdeteksi {$pageCountFinal} halaman.");
        }
    }
}
