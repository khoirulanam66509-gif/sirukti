<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordNotMustChange
{
    /**
     * Memastikan akun masih aktif dan memaksa pengguna mengganti password bila flag wajib_ganti_password aktif.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->status_aktif) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['username' => 'Akun Anda telah dinonaktifkan. Silakan hubungi kantor balai kampung.']);
        }

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
