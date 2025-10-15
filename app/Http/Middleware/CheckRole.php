<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }
        if (!in_array($user->role, $roles, true)) {
            abort(403, 'Akses ditolak.');
        }
        return $next($request);
    }
}