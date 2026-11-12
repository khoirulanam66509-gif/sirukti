<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordNotMustChange
{
    /**
     * Memaksa pengguna mengganti password bila flag wajib_ganti_password aktif.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->wajib_ganti_password) {
            // Izinkan akses rute ubah password dan logout
            if (! $request->routeIs('password.change', 'password.update', 'logout')) {
                return redirect()->route('password.change')
                    ->with('warning', 'Anda wajib mengganti password sementara terlebih dahulu demi keamanan akun Anda.');
            }
        }

        return $next($request);
    }
}
