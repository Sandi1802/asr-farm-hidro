<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:super_admin,admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // it_admin always has access
        if ($user->role_agri === 'it_admin') {
            return $next($request);
        }

        if (!in_array($user->role_agri, $roles)) {
            // atasan can view everything if they have access or we just give them global read?
            // Actually, if atasan is not in $roles, they shouldn't access it unless we say atasan has global read.
            // Let's assume atasan has global read access.
            if ($user->role_agri === 'atasan') {
                if (!in_array($request->method(), ['GET', 'HEAD'])) {
                    abort(403, 'Role Atasan hanya memiliki hak akses untuk melihat data (Read-Only).');
                }
                return $next($request);
            }
            
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // If they are in roles but it's atasan, still restrict to GET
        if ($user->role_agri === 'atasan' && !in_array($request->method(), ['GET', 'HEAD'])) {
            abort(403, 'Role Atasan hanya memiliki hak akses untuk melihat data (Read-Only).');
        }

        return $next($request);
    }
}
