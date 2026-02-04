<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when not authenticated.
     */
    protected function redirectTo($request)
    {
        // API requests should get JSON response, no redirect
        if (!$request->expectsJson()) {
            // Optional fallback for web
            return route('login'); // define login route if needed
        }

        return null;
    }
}
