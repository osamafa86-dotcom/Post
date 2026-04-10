<?php

namespace App\Http\Middleware;

use App\Services\StaticPageRenderer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class ServeStaticPage
{
    protected StaticPageRenderer $renderer;

    public function __construct(StaticPageRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Self-healing static cache middleware (stale-while-revalidate).
     *
     * Three paths:
     *   FRESH  → serve cached HTML instantly
     *   STALE  → serve cached HTML instantly + schedule background regen
     *   MISS   → let Laravel render, capture response, seed cache for next visitor
     *
     * No queue worker required. Works on any PHP setup.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ── Gate: only homepage GET for anonymous visitors ──────────
        if (!$this->shouldIntercept($request)) {
            return $next($request);
        }

        $locale = config('app.website_locale', 'ar');
        $device = $this->detectDevice($request);
        $path   = $this->renderer->resolve($locale, $device);

        // ── HIT: file exists ───────────────────────────────────────
        if ($path !== null) {
            $age = $this->renderer->age($locale, $device);

            // STALE: serve immediately, regenerate after response sent
            if ($age > StaticPageRenderer::STALE_AFTER) {
                $this->scheduleRegeneration($locale, $device);
                return $this->serveFile($path, $locale, $device, 'STALE');
            }

            // FRESH: serve directly
            return $this->serveFile($path, $locale, $device, 'HIT');
        }

        // ── MISS: no cached file exists ────────────────────────────
        // Let Laravel handle this request (first visitor pays the cost).
        // After the response is built, capture it and seed the cache.
        $response = $next($request);

        // Capture in terminating callback so it doesn't delay the response
        $captureResponse = $response;
        $captureRenderer = $this->renderer;
        app()->terminating(function () use ($captureRenderer, $captureResponse, $locale, $device) {
            try {
                $captureRenderer->storeFromResponse($captureResponse, $locale, $device);
            } catch (\Throwable $e) {
                // Silently fail — next request will try again
            }
        });

        // Make the MISS response CDN-cacheable so Cloudflare caches it at edge
        $this->makeCdnCacheable($response);

        return $response;
    }

    /**
     * Determine if this request should be intercepted by the static cache.
     */
    protected function shouldIntercept(Request $request): bool
    {
        // Only GET requests to homepage
        if (!$request->isMethod('GET') || $request->getPathInfo() !== '/') {
            return false;
        }

        // Disable static cache for sites that use interactive Livewire components on homepage
        if (in_array(config('app.launch'), ['fimed', 'alahed'])) {
            return false;
        }

        // Internal render request (from regeneration HTTP self-call)
        if ($request->header('X-Static-Render') === $this->renderer->getBypassToken()) {
            return false;
        }

        // Livewire AJAX
        if ($request->hasHeader('X-Livewire')) {
            return false;
        }

        // Dynamic upgrade from static page JS interceptor
        if ($request->query('_upgrade') === '1') {
            return false;
        }

        // Remember-me cookie → authenticated user, bypass static cache
        foreach ($request->cookies->keys() as $cookieName) {
            if (str_starts_with($cookieName, 'remember_web_')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Schedule background regeneration via app()->terminating().
     * Runs after the response is sent to the client — zero visitor impact.
     */
    protected function scheduleRegeneration(string $locale, string $device): void
    {
        $renderer = $this->renderer;
        app()->terminating(function () use ($renderer, $locale, $device) {
            // Flush output to client first (PHP-FPM)
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            }
            $renderer->regenerateInBackground($locale, $device);
        });
    }

    /**
     * Build an HTTP response from a static HTML file.
     */
    protected function serveFile(string $path, string $locale, string $device, string $cacheStatus): Response
    {
        $html = File::get($path);
        $lastModified = File::lastModified($path);

        return response($html, 200, [
            'Content-Type'                 => 'text/html; charset=UTF-8',
            'Last-Modified'                => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
            'X-Static-Cache'               => $cacheStatus,
            'X-Static-Variant'             => "{$locale}/{$device}",
            // Browser: no cache (CDN owns freshness). CDN: cache 60s, serve stale up to 120s while revalidating.
            'Cache-Control'                => 'public, max-age=0, s-maxage=60, stale-while-revalidate=120',
            'Cloudflare-CDN-Cache-Control'  => 'max-age=60, stale-while-revalidate=120',
            'CDN-Cache-Control'            => 'max-age=60, stale-while-revalidate=120',
        ]);
    }

    /**
     * Strip headers that prevent Cloudflare edge caching and apply CDN directives.
     * Only safe for anonymous, public-content responses.
     */
    protected function makeCdnCacheable(Response $response): Response
    {
        // Remove Set-Cookie — session cookies force Cloudflare to BYPASS
        $response->headers->remove('Set-Cookie');

        // Remove Vary: Cookie — causes per-visitor cache fragmentation at CDN
        $vary = $response->headers->get('Vary', '');
        $parts = array_filter(
            array_map('trim', explode(',', $vary)),
            fn ($v) => strcasecmp($v, 'Cookie') !== 0
        );
        if (!empty($parts)) {
            $response->headers->set('Vary', implode(', ', $parts));
        } else {
            $response->headers->remove('Vary');
        }

        // Set CDN cache directives
        $response->headers->set('Cache-Control', 'public, max-age=0, s-maxage=60, stale-while-revalidate=120');
        $response->headers->set('Cloudflare-CDN-Cache-Control', 'max-age=60, stale-while-revalidate=120');
        $response->headers->set('CDN-Cache-Control', 'max-age=60, stale-while-revalidate=120');

        return $response;
    }

    /**
     * Simple mobile detection based on User-Agent.
     */
    protected function detectDevice(Request $request): string
    {
        $ua = strtolower($request->userAgent() ?? '');

        $mobileKeywords = [
            'mobile', 'android', 'iphone', 'ipod', 'opera mini',
            'opera mobi', 'windows phone', 'blackberry', 'webos',
        ];

        foreach ($mobileKeywords as $keyword) {
            if (str_contains($ua, $keyword)) {
                return 'mobile';
            }
        }

        return 'desktop';
    }
}
