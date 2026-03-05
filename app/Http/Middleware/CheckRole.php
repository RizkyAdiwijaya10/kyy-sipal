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
     */
    public function handle(Request $request, Closure $next): Response
    {
         // Cek apakah user sudah login
         if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Cek apakah role user ada dalam daftar roles yang diizinkan
        if (!in_array(auth()->user()->role, $roles)) {
            // Jika tidak punya akses, tampilkan error 403
            abort(403, 'Unauthorized access. Halaman ini hanya untuk ' . implode(' atau ', $roles));
        }
        return $next($request);
    }
}
