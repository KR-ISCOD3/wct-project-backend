<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json(['error' => 'Unauthorized - User Not Authenticated'], 401);
        }

        // Check if the user's role is in the allowed roles
        if (!in_array($user->role, $roles)) {
            return response()->json(['error' => 'Unauthorized - Role Not Allowed'], 403);
        }

        return $next($request);
    }
}

