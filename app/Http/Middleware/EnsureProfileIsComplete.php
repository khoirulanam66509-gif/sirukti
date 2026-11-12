<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Memastikan profil kependudukan warga telah lengkap sebelum dapat mengajukan surat.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Hanya dicek untuk akun dengan role warga
        if ($user && $user->role === 'warga') {
            $warga = $user->warga;

            if (! $warga || ! $warga->profilLengkap()) {
                return redirect()->route('profile.edit')
                    ->with('warning', 'Mohon lengkapi profil kependudukan Anda terlebih dahulu sebelum mengajukan permohonan surat.');
            }
        }

        return $next($request);
    }
}
