<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\StatusLog;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseProgrammableObjectsTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $penggunaWarga;

    protected Pengguna $penggunaKasi;

    protected Warga $warga;

    protected Layanan $layanan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->penggunaWarga = Pengguna::create([
            'nama' => 'Warga Test SP',
            'username' => '1802010101990033',
            'password' => Hash::make('password'),
            'role' => 'warga',
        ]);

        $this->penggunaKasi = Pengguna::create([
            'nama' => 'Kasi Test SP',
            'username' => 'kasi_sp_test',
            'password' => Hash::make('password'),
            'role' => 'kasi',
        ]);

        $this->warga = Warga::create([
            'id_pengguna' => $this->penggunaWarga->id_pengguna,
            'nik' => '1802010101990033',
        ]);

        $this->layanan = Layanan::create([
            'kode' => 'PROG',
            'nama_layanan' => 'Layanan Programmable',
            'urutan' => 99,
        ]);
    }

    public function test_trigger_trg_pengajuan_ai_records_initial_status_log(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261019-0001',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'data_isian' => ['keperluan' => 'Test AI Trigger'],
            'id_pengubah' => $this->penggunaWarga->id_pengguna,
        ]);

        $log = StatusLog::where('id_pengajuan', $pengajuan->id_pengajuan)->first();

        $this->assertNotNull($log, 'Log status awal harus dibuat oleh trigger AI');
        $this->assertNull($log->status_lama);
        $this->assertEquals('menunggu', $log->status_baru);
        $this->assertEquals('Pengajuan dibuat', $log->catatan);
        $this->assertEquals($this->penggunaWarga->id_pengguna, $log->id_pengguna);
    }

    public function test_trigger_trg_pengajuan_au_records_status_change(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261019-0002',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'data_isian' => ['keperluan' => 'Test AU Trigger'],
            'id_pengubah' => $this->penggunaWarga->id_pengguna,
        ]);

        // Kasi mengubah status menjadi kurang_berkas
        $pengajuan->update([
            'status' => 'kurang_berkas',
            'id_pengubah' => $this->penggunaKasi->id_pengguna,
            'catatan_petugas' => 'KTP buram',
        ]);

        $logs = StatusLog::where('id_pengajuan', $pengajuan->id_pengajuan)->orderBy('id_log')->get();

        $this->assertCount(2, $logs);
        $secondLog = $logs[1];
        $this->assertEquals('menunggu', $secondLog->status_lama);
        $this->assertEquals('kurang_berkas', $secondLog->status_baru);
        $this->assertEquals('KTP buram', $secondLog->catatan);
        $this->assertEquals($this->penggunaKasi->id_pengguna, $secondLog->id_pengguna);
    }

    public function test_trigger_trg_pengajuan_bu_fills_tgl_diproses_and_tgl_selesai(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261019-0003',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'data_isian' => ['keperluan' => 'Test BU Dates'],
            'id_pengubah' => $this->penggunaWarga->id_pengguna,
        ]);

        $this->assertNull($pengajuan->tgl_diproses);
        $this->assertNull($pengajuan->tgl_selesai);

        // Update ke diproses
        $pengajuan->update(['status' => 'diproses']);
        $this->assertNotNull($pengajuan->fresh()->tgl_diproses);

        // Update ke selesai
        $pengajuan->update(['status' => 'selesai']);
        $this->assertNotNull($pengajuan->fresh()->tgl_selesai);
    }

    public function test_trigger_trg_pengajuan_bu_blocks_modifying_final_status(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261019-0004',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'data_isian' => ['keperluan' => 'Test Final Status'],
            'id_pengubah' => $this->penggunaWarga->id_pengguna,
        ]);

        $pengajuan->update(['status' => 'selesai']);

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('Pengajuan berstatus final tidak dapat diubah statusnya');

        // Mencoba mengubah dari selesai ke status lain harus ditolak trigger
        $pengajuan->update(['status' => 'diproses']);
    }

    public function test_sp_ambil_nomor_surat_increments_and_resets_per_year(): void
    {
        $tahun2026 = 2026;
        $tahun2027 = 2027;

        // Panggilan 1 tahun 2026 -> nomor 1
        DB::statement('CALL sp_ambil_nomor_surat(?, ?, @p_urut)', [$this->layanan->id_layanan, $tahun2026]);
        $urut1 = DB::select('SELECT @p_urut AS urut')[0]->urut;
        $this->assertEquals(1, $urut1);

        // Panggilan 2 tahun 2026 -> nomor 2 (berurutan)
        DB::statement('CALL sp_ambil_nomor_surat(?, ?, @p_urut)', [$this->layanan->id_layanan, $tahun2026]);
        $urut2 = DB::select('SELECT @p_urut AS urut')[0]->urut;
        $this->assertEquals(2, $urut2);

        // Panggilan 3 tahun 2027 -> nomor reset ke 1
        DB::statement('CALL sp_ambil_nomor_surat(?, ?, @p_urut)', [$this->layanan->id_layanan, $tahun2027]);
        $urutTahunBaru = DB::select('SELECT @p_urut AS urut')[0]->urut;
        $this->assertEquals(1, $urutTahunBaru);
    }

    public function test_sp_rekap_periode_and_views(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261019-0005',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'data_isian' => ['keperluan' => 'Test Rekap'],
            'id_pengubah' => $this->penggunaWarga->id_pengguna,
            'tgl_pengajuan' => Carbon::now(),
        ]);

        // Cek View v_antrean_pengajuan
        $antrean = DB::table('v_antrean_pengajuan')
            ->where('id_pengajuan', $pengajuan->id_pengajuan)
            ->first();

        $this->assertNotNull($antrean);
        $this->assertEquals($pengajuan->no_pengajuan, $antrean->no_pengajuan);
        $this->assertEquals($this->penggunaWarga->nama, $antrean->nama_warga);

        // Uji soft delete pada view
        $pengajuan->delete();
        $antreanAfterDelete = DB::table('v_antrean_pengajuan')
            ->where('id_pengajuan', $pengajuan->id_pengajuan)
            ->first();
        $this->assertNull($antreanAfterDelete, 'View v_antrean_pengajuan tidak boleh memuat pengajuan yang di-soft-delete');

        // Cek SP sp_rekap_periode
        $rekap = DB::select('CALL sp_rekap_periode(?, ?)', [
            Carbon::now()->subDays(7)->toDateString(),
            Carbon::now()->addDays(1)->toDateString(),
        ]);

        $this->assertIsArray($rekap);
        $this->assertNotEmpty($rekap);
    }
}
