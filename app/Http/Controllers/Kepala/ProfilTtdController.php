<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kepala\UpdateProfilKepalaRequest;
use App\Http\Requests\Kepala\UpdateStempelRequest;
use App\Http\Requests\Kepala\UpdateTtdRequest;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfilTtdController extends Controller
{
    /**
     * Menampilkan halaman profil dan pengelolaan tanda tangan digital (Layar P3).
     */
    public function edit(Request $request): View
    {
        return view('kepala.profil.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Memperbarui informasi profil aparatur Kepala Kampung.
     */
    public function updateProfil(UpdateProfilKepalaRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'nama' => $request->validated('nama'),
            'nip' => $request->validated('nip') ?: null,
            'jabatan' => $request->validated('jabatan'),
            'no_hp' => $request->validated('no_hp') ?: null,
        ]);

        AuditLogService::catat(
            'Memperbarui Profil Kepala Kampung',
            'Profil & TTD Kepala',
            $user->nama,
            ['jabatan' => $user->jabatan]
        );

        return redirect()->route('kepala.profil.edit')
            ->with('status_profil', 'Data profil aparatur berhasil diperbarui.');
    }

    /**
     * Mengunggah atau memperbarui tanda tangan digital Kepala Kampung.
     */
    public function updateTtd(UpdateTtdRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Hapus file tanda tangan lama jika ada
        if ($user->file_ttd && Storage::disk('private')->exists($user->file_ttd)) {
            Storage::disk('private')->delete($user->file_ttd);
        }

        // Simpan file tanda tangan baru ke direktori privat
        $path = $request->file('file_ttd')->store('ttd', 'private');

        $user->update([
            'file_ttd' => $path,
        ]);

        AuditLogService::catat(
            'Mengunggah Tanda Tangan Digital',
            'Profil & TTD Kepala',
            $user->nama
        );

        return redirect()->route('kepala.profil.edit')
            ->with('status_ttd', 'Tanda tangan digital berhasil diperbarui dan siap digunakan untuk penandatanganan surat.');
    }

    /**
     * Menghapus tanda tangan digital Kepala Kampung.
     */
    public function destroyTtd(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->file_ttd && Storage::disk('private')->exists($user->file_ttd)) {
            Storage::disk('private')->delete($user->file_ttd);
        }

        $user->update([
            'file_ttd' => null,
        ]);

        AuditLogService::catat(
            'Menghapus Tanda Tangan Digital',
            'Profil & TTD Kepala',
            $user->nama
        );

        return redirect()->route('kepala.profil.edit')
            ->with('status_ttd', 'Tanda tangan digital telah dihapus. Anda tidak dapat menyetujui surat sebelum mengunggah tanda tangan baru.');
    }

    /**
     * Pratinjau streaming tanda tangan digital secara aman dan privat.
     */
    public function previewTtd(Request $request): StreamedResponse
    {
        $user = $request->user();

        if ($user->role !== 'kepala' || empty($user->file_ttd)) {
            abort(404);
        }

        if (! Storage::disk('private')->exists($user->file_ttd)) {
            abort(404);
        }

        return Storage::disk('private')->response($user->file_ttd);
    }

    /**
     * Mengunggah atau memperbarui stempel resmi Kepala Kampung.
     */
    public function updateStempel(UpdateStempelRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Hapus file stempel lama jika ada
        if ($user->file_stempel && Storage::disk('private')->exists($user->file_stempel)) {
            Storage::disk('private')->delete($user->file_stempel);
        }

        // Simpan file stempel baru ke direktori privat
        $path = $request->file('file_stempel')->store('stempel', 'private');

        $user->update([
            'file_stempel' => $path,
        ]);

        AuditLogService::catat(
            'Mengunggah Stempel Resmi Kampung',
            'Profil & TTD Kepala',
            $user->nama
        );

        return redirect()->route('kepala.profil.edit')
            ->with('status_stempel', 'Stempel resmi kampung berhasil diperbarui dan siap digunakan untuk pengesahan surat.');
    }

    /**
     * Menghapus stempel resmi Kepala Kampung.
     */
    public function destroyStempel(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->file_stempel && Storage::disk('private')->exists($user->file_stempel)) {
            Storage::disk('private')->delete($user->file_stempel);
        }

        $user->update([
            'file_stempel' => null,
        ]);

        AuditLogService::catat(
            'Menghapus Stempel Resmi Kampung',
            'Profil & TTD Kepala',
            $user->nama
        );

        return redirect()->route('kepala.profil.edit')
            ->with('status_stempel', 'Stempel resmi kampung telah dihapus.');
    }

    /**
     * Pratinjau streaming stempel resmi secara aman dan privat.
     */
    public function previewStempel(Request $request): StreamedResponse
    {
        $user = $request->user();

        if ($user->role !== 'kepala' || empty($user->file_stempel)) {
            abort(404);
        }

        if (! Storage::disk('private')->exists($user->file_stempel)) {
            abort(404);
        }

        return Storage::disk('private')->response($user->file_stempel);
    }
}
