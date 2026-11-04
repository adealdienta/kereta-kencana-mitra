<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePelangganRole
{
    /**
     * Pastikan pengguna mobile yang terautentikasi memiliki role 'pelanggan'
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Silakan masuk terlebih dahulu.',
            ], 401);
        }

        if ($user->role !== 'pelanggan') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Aplikasi mobile ini hanya diperuntukkan bagi akun mitra toko (pelanggan).',
            ], 403);
        }

        return $next($request);
    }
}
