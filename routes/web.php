<?php

use App\Http\Controllers\ArsipLaporanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kasi\PengaturanController;
use App\Http\Controllers\Kasi\PersyaratanLayananController;
use App\Http\Controllers\Kasi\WargaController;
use App\Http\Controllers\Kepala\PersetujuanController;
use App\Http\Controllers\Kepala\ProfilTtdController;
use App\Http\Controllers\LacakPengajuanController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VerifikasiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/lacak', [LacakPengajuanController::class, 'index'])
    ->middleware('throttle:60,1')
    ->name('lacak');
Route::get('/verifikasi/{kode}', [VerifikasiController::class, 'show'])
    ->middleware('throttle:60,1')
    ->name('verifikasi.show');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile', fn () => redirect()->route('profile.edit'));

    // Pengajuan Surat oleh Warga
    Route::get('/riwayat', [PengajuanController::class, 'index'])
        ->middleware(['role:warga', 'throttle:60,1'])
        ->name('riwayat');
    Route::get('/pengajuan/buat', [PengajuanController::class, 'create'])
        ->middleware(['role:warga', 'profil.lengkap'])
        ->name('pengajuan.buat');
    Route::post('/pengajuan', [PengajuanController::class, 'store'])
        ->middleware(['role:warga', 'profil.lengkap'])
        ->name('pengajuan.store');
    Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])
        ->name('pengajuan.show');
    Route::post('/pengajuan/{pengajuan}/unggah-ulang', [PengajuanController::class, 'unggahUlang'])
        ->middleware(['role:warga'])
        ->name('pengajuan.unggah-ulang');
    Route::post('/pengajuan/{pengajuan}/batalkan', [PengajuanController::class, 'batalkan'])
        ->middleware(['role:warga'])
        ->name('pengajuan.batalkan');
    Route::get('/pengajuan/{pengajuan}/status', [PengajuanController::class, 'statusJson'])
        ->name('pengajuan.status-json');
    Route::get('/pengajuan/{pengajuan}/unduh-surat', [PengajuanController::class, 'downloadSurat'])
        ->name('surat.unduh');
    Route::get('/pengajuan/{pengajuan}/berkas/{berkas}', [PengajuanController::class, 'showBerkas'])
        ->name('pengajuan.berkas');

    // Manajemen dan Verifikasi Pengajuan oleh Kasi
    Route::middleware(['role:kasi'])->prefix('kasi')->name('kasi.')->group(function () {
        Route::get('/antrean', [App\Http\Controllers\Kasi\PengajuanController::class, 'index'])
            ->middleware('throttle:60,1')
            ->name('pengajuan.index');
        Route::get('/antrean/{pengajuan}', [App\Http\Controllers\Kasi\PengajuanController::class, 'show'])->name('pengajuan.show');
        Route::get('/antrean/{pengajuan}/draft', [App\Http\Controllers\Kasi\PengajuanController::class, 'pratinjauDraft'])->name('pengajuan.draft');
        Route::get('/antrean/{pengajuan}/draft/pdf', [App\Http\Controllers\Kasi\PengajuanController::class, 'streamDraftPdf'])->name('pengajuan.draft.pdf');
        Route::post('/antrean/{pengajuan}/verifikasi', [App\Http\Controllers\Kasi\PengajuanController::class, 'verifikasi'])->name('pengajuan.verifikasi');
        Route::get('/pengaturan', [PengaturanController::class, 'edit'])->name('pengaturan.edit');
        Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');

        // K5: Kelola Warga
        Route::get('/warga', [WargaController::class, 'index'])
            ->middleware('throttle:60,1')
            ->name('warga.index');
        Route::get('/warga/{warga}/edit', [WargaController::class, 'edit'])->name('warga.edit');
        Route::put('/warga/{warga}', [WargaController::class, 'update'])->name('warga.update');
        Route::post('/warga/{warga}/reset-password', [WargaController::class, 'resetPassword'])->name('warga.reset-password');
        Route::post('/warga/{warga}/toggle-status', [WargaController::class, 'toggleStatus'])->name('warga.toggle-status');

        // K6: Persyaratan Layanan
        Route::get('/layanan', [PersyaratanLayananController::class, 'index'])->name('layanan.index');
        Route::get('/persyaratan', [PersyaratanLayananController::class, 'index'])->name('persyaratan.index');
        Route::post('/layanan/{layanan}/syarat', [PersyaratanLayananController::class, 'storeSyarat'])->name('layanan.syarat.store');
        Route::put('/layanan/{layanan}/syarat/{syarat}', [PersyaratanLayananController::class, 'updateSyarat'])->name('layanan.syarat.update');
        Route::delete('/layanan/{layanan}/syarat/{syarat}', [PersyaratanLayananController::class, 'destroySyarat'])->name('layanan.syarat.destroy');
        Route::post('/layanan/{layanan}/syarat/{syarat}/toggle', [PersyaratanLayananController::class, 'toggleStatusSyarat'])->name('layanan.syarat.toggle');
    });

    // Profil, Persetujuan, dan Tanda Tangan Digital Kepala Kampung
    Route::middleware(['role:kepala'])->prefix('kepala')->name('kepala.')->group(function () {
        Route::get('/persetujuan', [PersetujuanController::class, 'index'])->name('persetujuan.index');
        Route::get('/persetujuan/{pengajuan}', [PersetujuanController::class, 'show'])->name('persetujuan.show');
        Route::get('/persetujuan/{pengajuan}/preview-pdf', [PersetujuanController::class, 'streamPreviewPdf'])->name('persetujuan.preview');
        Route::post('/persetujuan/{pengajuan}/setujui', [PersetujuanController::class, 'setujui'])->name('persetujuan.setujui');
        Route::post('/persetujuan/{pengajuan}/kembalikan', [PersetujuanController::class, 'kembalikan'])->name('persetujuan.kembalikan');

        Route::get('/profil-ttd', [ProfilTtdController::class, 'edit'])->name('profil.edit');
        Route::post('/profil-ttd/profil', [ProfilTtdController::class, 'updateProfil'])->name('profil.update');
        Route::post('/profil-ttd/ttd', [ProfilTtdController::class, 'updateTtd'])->name('ttd.update');
        Route::delete('/profil-ttd/ttd', [ProfilTtdController::class, 'destroyTtd'])->name('ttd.destroy');
        Route::get('/profil-ttd/preview', [ProfilTtdController::class, 'previewTtd'])->name('ttd.preview');

        // Stempel Resmi Kampung
        Route::post('/profil-ttd/stempel', [ProfilTtdController::class, 'updateStempel'])->name('stempel.update');
        Route::delete('/profil-ttd/stempel', [ProfilTtdController::class, 'destroyStempel'])->name('stempel.destroy');
        Route::get('/profil-ttd/preview-stempel', [ProfilTtdController::class, 'previewStempel'])->name('stempel.preview');
    });

    // K8 & P5: Arsip dan Laporan (Kasi & Kepala Kampung)
    Route::get('/arsip-laporan', [ArsipLaporanController::class, 'index'])->name('arsip.index');
    Route::get('/arsip-laporan/cetak', [ArsipLaporanController::class, 'cetakPdf'])->name('arsip.cetak');
    Route::get('/arsip-laporan/ekspor-csv', [ArsipLaporanController::class, 'eksporCsv'])->name('arsip.ekspor-csv');
    Route::delete('/arsip-laporan/{pengajuan}', [ArsipLaporanController::class, 'destroy'])->name('arsip.destroy');
});

if (config('app.debug')) {
    Route::get('/design-system', function () {
        return view('design-system');
    })->name('design-system');
}

require __DIR__.'/auth.php';
