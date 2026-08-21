<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApiRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $userRole = $user->role_agri;

        // it_admin has full access
        if ($userRole === 'it_admin') {
            return $next($request);
        }

        // atasan is read-only (only GET/HEAD allowed)
        if ($userRole === 'atasan') {
            if (!$request->isMethod('get') && !$request->isMethod('head')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden. Atasan hanya memiliki akses read-only.'
                ], 403);
            }
            return $next($request);
        }

        // Check if user's role_agri is in the allowed roles list
        if (!empty($roles) && !in_array($userRole, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Akses ditolak untuk role ini.'
            ], 403);
        }

        return $next($request);
    }
}
