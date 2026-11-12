<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BacklogOpsionalPrioritasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_o3_kasi_dan_kepala_dapat_mengunduh_ekspor_csv_dengan_utf8_bom(): void
    {
        $kasi = Pengguna::where('role', 'kasi')->first();
        $kepala = Pengguna::where('role', 'kepala')->first();
        $bulan = (int) date('n');
        $tahun = (int) date('Y');

        // Kasi mengunduh CSV
        $responseKasi = $this->actingAs($kasi)->get("/arsip-laporan/ekspor-csv?bulan={$bulan}&tahun={$tahun}");
        $responseKasi->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $responseKasi->headers->get('content-type'));
        $this->assertStringContainsString("Arsip_Laporan_SIRUKTI_{$tahun}_{$bulan}.csv", (string) $responseKasi->headers->get('content-disposition'));

        $csvContent = $responseKasi->streamedContent();
        // Pastikan diawali UTF-8 BOM agar ramah Microsoft Excel
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csvContent);
        $this->assertStringContainsString('REKAPITULASI PER JENIS LAYANAN', $csvContent);
        $this->assertStringContainsString('DAFTAR RINCIAN ARSIP PENGAJUAN SURAT', $csvContent);

        // Kepala juga dapat mengunduh CSV
        $responseKepala = $this->actingAs($kepala)->get("/arsip-laporan/ekspor-csv?bulan={$bulan}&tahun={$tahun}");
        $responseKepala->assertStatus(200);
    }

    public function test_o3_warga_ditolak_mengakses_ekspor_csv(): void
    {
        $warga = Pengguna::where('role', 'warga')->first();

        $response = $this->actingAs($warga)->get('/arsip-laporan/ekspor-csv');
        $response->assertStatus(403);
    }

    public function test_o3_halaman_arsip_menampilkan_tombol_ekspor_csv_dan_bar_visual_rekap(): void
    {
        $kasi = Pengguna::where('role', 'kasi')->first();

        $response = $this->actingAs($kasi)->get('/arsip-laporan');
        $response->assertStatus(200);
        $response->assertSee('Ekspor Excel/CSV');
        $response->assertSee('Visualisasi Proporsi Pengajuan per Layanan');
    }

    public function test_o6_aktivitas_petugas_tercatat_otomatis_di_audit_log_dan_tampil_di_tab_arsip(): void
    {
        $kasi = Pengguna::where('role', 'kasi')->first();
        $warga = Warga::with('pengguna')->first();
        $layanan = Layanan::first();

        // 1. Kasi mereset password warga
        $this->actingAs($kasi)->post(route('kasi.warga.reset-password', $warga), [
            'password_baru' => 'sandibaru123',
        ])->assertRedirect();

        $this->assertDatabaseHas('audit_log', [
            'id_pengguna' => $kasi->id_pengguna,
            'aksi' => 'Reset Kata Sandi Warga',
            'modul' => 'Kelola Warga',
            'target_label' => $warga->pengguna->nama,
        ]);

        // Pastikan password tidak bocor di detail_perubahan
        $logReset = AuditLog::where('aksi', 'Reset Kata Sandi Warga')->latest('id_audit')->first();
        $this->assertNotNull($logReset);
        $this->assertArrayNotHasKey('password', $logReset->detail_perubahan ?? []);
        $this->assertArrayNotHasKey('password_baru', $logReset->detail_perubahan ?? []);

        // 2. Kasi menambah syarat layanan
        $this->actingAs($kasi)->post(route('kasi.layanan.syarat.store', $layanan), [
            'nama_syarat' => 'Surat Pengantar Khusus Test',
            'petunjuk' => 'Lampirkan bila ada',
            'wajib' => '0',
            'status_aktif' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('audit_log', [
            'id_pengguna' => $kasi->id_pengguna,
            'aksi' => 'Menambah Persyaratan Layanan',
            'modul' => 'Persyaratan Layanan',
        ]);

        // 3. Kasi menghapus arsip (soft delete)
        $pengajuan = Pengajuan::first();
        $this->actingAs($kasi)->delete(route('arsip.destroy', $pengajuan))->assertRedirect();

        $this->assertDatabaseHas('audit_log', [
            'id_pengguna' => $kasi->id_pengguna,
            'aksi' => 'Menghapus Arsip Surat (Soft Delete)',
            'modul' => 'Arsip & Laporan',
        ]);

        // 4. Pastikan tampil pada Tab 3 (Log Aktivitas) di halaman Arsip & Laporan
        $responseArsip = $this->actingAs($kasi)->get('/arsip-laporan');
        $responseArsip->assertStatus(200);
        $responseArsip->assertSee('Log Aktivitas');
        $responseArsip->assertSee('Jejak Audit Aktivitas Petugas');
        $responseArsip->assertSee('Reset Kata Sandi Warga');
        $responseArsip->assertSee('Menambah Persyaratan Layanan');
        $responseArsip->assertSee('Menghapus Arsip Surat (Soft Delete)');
    }

    public function test_o8_pwa_manifest_service_worker_dan_meta_tag_tersedia(): void
    {
        // Pastikan file fisik PWA ada di public/
        $this->assertFileExists(public_path('manifest.webmanifest'));
        $this->assertFileExists(public_path('sw.js'));
        $this->assertFileExists(public_path('icons/icon-192.png'));
        $this->assertFileExists(public_path('icons/icon-512.png'));
        $this->assertFileExists(public_path('icons/icon.svg'));

        $manifest = json_decode((string) file_get_contents(public_path('manifest.webmanifest')), true);
        $this->assertIsArray($manifest);
        $this->assertEquals('SIRUKTI', $manifest['short_name']);
        $this->assertEquals('standalone', $manifest['display']);
        $this->assertEquals('#2F5D3A', $manifest['theme_color']);

        // Pastikan halaman publik memuat link manifest, registrasi service worker, dan tombol ringkas Instal PWA
        $landing = $this->get('/');
        $landing->assertStatus(200);
        $landing->assertSee('rel="manifest" href="/manifest.webmanifest"', false);
        $landing->assertSee('navigator.serviceWorker.register(\'/sw.js\')', false);
        $landing->assertSee('Instal');
        $landing->assertSee('Instal Aplikasi SIRUKTI');

        $login = $this->get('/login');
        $login->assertStatus(200);
        $login->assertSee('rel="manifest" href="/manifest.webmanifest"', false);
        $login->assertSee('Instal');

        // Setelah login (menu profil / dashboard) tidak menampilkan tombol pasang shortcut
        $warga = Pengguna::where('role', 'warga')->first();
        $dashboard = $this->actingAs($warga)->get('/dashboard');
        $dashboard->assertStatus(200);
        $dashboard->assertSee('rel="manifest" href="/manifest.webmanifest"', false);
        $dashboard->assertDontSee('Pasang Shortcut Aplikasi');
    }
}
