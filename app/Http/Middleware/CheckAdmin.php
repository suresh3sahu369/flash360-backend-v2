<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            // Not logged in
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if ($user->role !== 'admin') {
            // Logged in but not admin
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
