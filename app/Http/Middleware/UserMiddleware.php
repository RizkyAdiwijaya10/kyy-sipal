<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Cek apakah role user adalah 'user'
        if (auth()->user()->role !== 'user') {
            // Jika bukan user, tampilkan error 403
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Pengguna Biasa.');
        }

        return $next($request);
    }
}