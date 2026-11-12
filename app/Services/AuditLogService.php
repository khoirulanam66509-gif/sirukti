<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AuditLogService
{
    /**
     * Mencatat aktivitas penting petugas secara aman tanpa membocorkan data sensitif (password/NIK utuh).
     */
    public static function catat(
        string $aksi,
        string $modul,
        ?string $targetLabel = null,
        ?array $detailPerubahan = null,
        ?int $idPengguna = null
    ): ?AuditLog {
        try {
            if (! Schema::hasTable('audit_log')) {
                return null;
            }

            // Sanitasi kunci sensitif bila ada
            if ($detailPerubahan !== null) {
                unset(
                    $detailPerubahan['password'],
                    $detailPerubahan['password_baru'],
                    $detailPerubahan['password_confirmation']
                );

                if (isset($detailPerubahan['nik']) && is_string($detailPerubahan['nik'])) {
                    $detailPerubahan['nik'] = samarkan_nik($detailPerubahan['nik']);
                }
            }

            return AuditLog::create([
                'id_pengguna' => $idPengguna ?? Auth::id(),
                'aksi' => $aksi,
                'modul' => $modul,
                'target_label' => $targetLabel,
                'detail_perubahan' => $detailPerubahan,
                'ip_address' => Request::ip(),
            ]);
        } catch (Throwable) {
            return null;
        }
    }
}
