<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prevents Livewire/Alpine JS from being auto-injected on the homepage.
 * The server still renders the Livewire component (HTML), but no client-side
 * framework JS is sent — achieving zero-framework runtime on initial load.
 */
class DisableLivewireOnHomepage
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('main.palestine_post.index')) {
            config(['livewire.inject_assets' => false]);
        }

        return $next($request);
    }
}
