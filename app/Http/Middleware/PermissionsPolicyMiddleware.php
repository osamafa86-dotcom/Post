<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionsPolicyMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Permissions-Policy', 'unload=*');

        return $response;
    }
}
