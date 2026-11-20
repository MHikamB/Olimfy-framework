<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Batasi akses route berdasarkan role pengguna.
     *
     * Role dibaca dari relasi database `roleRecord`, dengan fallback ke
     * kolom lama `role` selama masa transisi.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect('/login');
        }

        $userRole = $request->user()->roleRecord?->nama_role ?? $request->user()->role;

        if (! in_array($userRole, $roles, true)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}
