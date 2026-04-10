<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            if ($request->is('hongora/dashboard/') || $request->is('hongora/dashboard/*')) {
                return route('main.hongora.user_dashboard_login');
            }elseif ($request->is('maktoob/dashboard/') || $request->is('maktoob/dashboard/*')){
                return route('main.maktoob.user_dashboard_login');
            }
            return route('login');
        }
        return route('login');
    }
}
