<?php

namespace App\Console\Commands;

use App\Models\Dusun;
use App\Models\Pengguna;
use App\Models\Warga;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SiapDeployCommand extends Command
{
    /**
     * Nama dan signature perintah console.
     *
     * @var string
     */
    protected $signature = 'sirukti:siap-deploy
                            {--force : Jalankan pembersihan langsung tanpa konfirmasi interaktif}';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Bersihkan data dummy pengajuan, surat, berkas, counter, dan akun warga selain Kasi, Kepala, dan Anam untuk persiapan produksi';

    private const DEFAULT_TTD_PATH = 'ttd/z6hRYMdmKkgW9JuyhBGlxQKnvsKE1BNU08sFGM4H.png';

    private const DEFAULT_STEMPEL_PATH = 'stempel/7IJdzoaxVSTNYspz9zYipXUxoe9LTr8Fft89PeLE.png';

    /**
     * Jalankan perintah console.
     */
    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('Tindakan ini akan menghapus seluruh data pengajuan, surat, berkas dummy, counter surat, audit log, dan akun warga selain Anam. Lanjutkan?')) {
            $this->warn('Proses dibatalkan.');

            return self::SUCCESS;
        }

        $this->info('=== Persiapan Produksi SIRUKTI ===');

        $summary = [
            'surat' => 0,
            'berkas' => 0,
            'status_log' => 0,
            'pengajuan' => 0,
            'counter_surat' => 0,
            'audit_log' => 0,
            'warga_dihapus' => 0,
            'pengguna_dihapus' => 0,
        ];

        $disk = Storage::disk('private');

        DB::transaction(function () use (&$summary, $disk) {
            // 1. Hapus data transaksional sesuai urutan foreign key (restrictOnDelete)
            $summary['surat'] = DB::table('surat')->delete();
            $summary['berkas'] = DB::table('berkas_pengajuan')->delete();
            $summary['status_log'] = DB::table('status_log')->delete();
            $summary['pengajuan'] = DB::table('pengajuan')->delete();
            $summary['counter_surat'] = DB::table('counter_surat')->delete();
            $summary['audit_log'] = DB::table('audit_log')->delete();

            // 2. Pastikan akun Kasi & Kepala tersedia serta wajib ganti password saat login pertama
            $defaultPasswordHash = Hash::make('password123');

            $kasi = Pengguna::where('role', 'kasi')->orderBy('id_pengguna')->first();
            if (! $kasi) {
                $kasi = Pengguna::create([
                    'nama' => 'Budi Santoso, S.IP',
                    'username' => 'kasi',
                    'password' => $defaultPasswordHash,
                    'no_hp' => '081234567801',
                    'role' => 'kasi',
                    'jabatan' => 'Kasi Pelayanan',
                    'nip' => '198205122008011003',
                    'status_aktif' => true,
                    'wajib_ganti_password' => true,
                ]);
            } else {
                $kasi->update([
                    'status_aktif' => true,
                    'wajib_ganti_password' => true,
                ]);
            }

            $kepala = Pengguna::where('role', 'kepala')->orderBy('id_pengguna')->first();
            if (! $kepala) {
                $kepala = Pengguna::create([
                    'nama' => 'I Wayan Sukadana',
                    'username' => 'kepala',
                    'password' => $defaultPasswordHash,
                    'no_hp' => '081234567802',
                    'role' => 'kepala',
                    'jabatan' => 'Kepala Kampung',
                    'nip' => '197508152005011002',
                    'file_ttd' => $disk->exists(self::DEFAULT_TTD_PATH) ? self::DEFAULT_TTD_PATH : null,
                    'file_stempel' => $disk->exists(self::DEFAULT_STEMPEL_PATH) ? self::DEFAULT_STEMPEL_PATH : null,
                    'status_aktif' => true,
                    'wajib_ganti_password' => true,
                ]);
            } else {
                $ttdPath = $kepala->file_ttd && $disk->exists($kepala->file_ttd)
                    ? $kepala->file_ttd
                    : ($disk->exists(self::DEFAULT_TTD_PATH) ? self::DEFAULT_TTD_PATH : null);

                $stempelPath = $kepala->file_stempel && $disk->exists($kepala->file_stempel)
                    ? $kepala->file_stempel
                    : ($disk->exists(self::DEFAULT_STEMPEL_PATH) ? self::DEFAULT_STEMPEL_PATH : null);

                $kepala->update([
                    'file_ttd' => $ttdPath,
                    'file_stempel' => $stempelPath,
                    'status_aktif' => true,
                    'wajib_ganti_password' => true,
                ]);
            }

            // 3. Pastikan akun warga Anam tersedia & dipertahankan
            $anam = Pengguna::where('role', 'warga')
                ->where(function ($query) {
                    $query->where('username', 'anam')
                        ->orWhere('nama', 'like', '%Anam%')
                        ->orWhereHas('warga', fn ($w) => $w->where('nik', '1234567891011121'));
                })
                ->first();

            if (! $anam) {
                $anam = Pengguna::create([
                    'nama' => 'Khoirul Anam',
                    'username' => 'anam',
                    'password' => $defaultPasswordHash,
                    'no_hp' => '081234567803',
                    'role' => 'warga',
                    'status_aktif' => true,
                    'wajib_ganti_password' => false,
                ]);
            } else {
                $anam->update(['status_aktif' => true]);
            }

            if (! $anam->warga) {
                $idDusunDefault = Dusun::orderBy('id_dusun')->value('id_dusun');
                Warga::create([
                    'id_pengguna' => $anam->id_pengguna,
                    'nik' => '1234567891011121',
                    'no_kk' => '1234567891011120',
                    'tempat_lahir' => 'Lampung Tengah',
                    'tgl_lahir' => '2000-01-01',
                    'jenis_kelamin' => 'L',
                    'agama' => 'Islam',
                    'status_perkawinan' => 'belum_kawin',
                    'pekerjaan' => 'Belum Bekerja',
                    'alamat' => 'RT 001 / RW 001',
                    'id_dusun' => $idDusunDefault,
                    'rt' => '001',
                    'rw' => '001',
                ]);
            }

            $keepIds = [$kasi->id_pengguna, $kepala->id_pengguna, $anam->id_pengguna];

            // 4. Hapus warga dan pengguna selain 3 akun di atas
            $summary['warga_dihapus'] = DB::table('warga')
                ->whereNotIn('id_pengguna', $keepIds)
                ->delete();

            $summary['pengguna_dihapus'] = DB::table('pengguna')
                ->whereNotIn('id_pengguna', $keepIds)
                ->delete();
        });

        // 5. Bersihkan file dummy di storage/app/private (tanpa menyentuh TTD & stempel aktif)
        foreach (['berkas', 'berkas_dummy', 'surat', 'surat_selesai'] as $dir) {
            $this->hapusDirektoriPenuh($disk->path($dir));
        }

        $kepalaAktif = Pengguna::where('role', 'kepala')->first();
        $keepFiles = array_filter([
            $kepalaAktif?->file_ttd,
            $kepalaAktif?->file_stempel,
        ]);

        foreach (['ttd', 'stempel'] as $protectedDir) {
            $fullDir = $disk->path($protectedDir);
            if (File::isDirectory($fullDir)) {
                foreach ($disk->files($protectedDir) as $filePath) {
                    if (! in_array($filePath, $keepFiles, true)) {
                        if (! $disk->delete($filePath)) {
                            File::delete($disk->path($filePath));
                        }
                    }
                }
            }
        }

        // 6. Bersihkan file sesi lama & log pengujian
        $sessionFiles = glob(storage_path('framework/sessions/*')) ?: [];
        foreach ($sessionFiles as $sessionFile) {
            if (is_file($sessionFile) && basename($sessionFile) !== '.gitignore') {
                @unlink($sessionFile);
            }
        }

        $logPath = storage_path('logs/laravel.log');
        if (is_file($logPath)) {
            @file_put_contents($logPath, '');
        }

        $this->newLine();
        $this->info('Pembersihan data produksi selesai dijalankan.');
        $this->table(
            ['Entitas', 'Jumlah Dihapus'],
            [
                ['Surat Terbit', $summary['surat']],
                ['Berkas Pengajuan', $summary['berkas']],
                ['Status Log', $summary['status_log']],
                ['Pengajuan (termasuk Soft Delete)', $summary['pengajuan']],
                ['Counter Nomor Surat', $summary['counter_surat']],
                ['Audit Log', $summary['audit_log']],
                ['Data Warga Dummy', $summary['warga_dihapus']],
                ['Akun Pengguna Dummy', $summary['pengguna_dihapus']],
            ]
        );

        $this->table(
            ['Username', 'Nama', 'Role', 'Wajib Ganti Password', 'TTD / Stempel'],
            Pengguna::orderBy('id_pengguna')->get()->map(fn (Pengguna $u) => [
                $u->username,
                $u->nama,
                strtoupper($u->role),
                $u->wajib_ganti_password ? 'YA' : 'TIDAK',
                $u->role === 'kepala'
                    ? (($u->file_ttd && $disk->exists($u->file_ttd) ? 'TTD OK' : 'TTD KOSONG').' | '.($u->file_stempel && $disk->exists($u->file_stempel) ? 'Stempel OK' : 'Stempel KOSONG'))
                    : '-',
            ])->all()
        );

        return self::SUCCESS;
    }

    /**
     * Hapus direktori beserta seluruh isinya secara aman pada Windows dan Linux.
     */
    private function hapusDirektoriPenuh(string $fullPath): void
    {
        if (! is_dir($fullPath)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fullPath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        $entries = [];
        foreach ($iterator as $item) {
            $entries[] = [$item->isDir() && ! $item->isLink(), $item->getPathname()];
        }

        unset($item, $iterator);
        gc_collect_cycles();

        foreach ($entries as [$isDir, $path]) {
            @chmod($path, 0777);
            if ($isDir) {
                @rmdir($path);
            } else {
                @unlink($path);
            }
        }

        @chmod($fullPath, 0777);
        @rmdir($fullPath);
    }
}
