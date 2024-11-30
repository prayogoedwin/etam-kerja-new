<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Pastikan pengguna sudah login
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Dapatkan role pengguna
        $userRole = Auth::user()->roles[0]['name'] ?? null;

        // Periksa apakah role pengguna cocok
        if (!in_array($userRole, $roles)) {
            // Tampilkan halaman akses ditolak
            return response()->view('errors.access_denied', [], 403);
        }

        return $next($request);
    }
}

