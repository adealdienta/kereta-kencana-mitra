<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string[]  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        $user = auth()->user();

        // Jika tidak ada batasan role atau role user cocok dengan salah satu role yang diizinkan
        if (empty($roles) || in_array($user->role, $roles)) {
            return $next($request);
        }

        // Khusus owner selalu memiliki akses super ke seluruh fitur
        if ($user->role === 'owner') {
            return $next($request);
        }

        abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk membuka halaman ini.');
    }
}
