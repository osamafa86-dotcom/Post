<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StaticPageRenderer
{
    protected string $cachePath;
    protected string $bypassToken;

    /** Max age in seconds before a cached page is considered stale. */
    public const STALE_AFTER = 60;

    /** Lock timeout — ignore stale locks older than this. */
    public const LOCK_TIMEOUT = 30;

    public function __construct()
    {
        $this->cachePath = storage_path('framework/cache/pages');
        $this->bypassToken = config('app.key');
    }

    // ─── Staleness & Resolution ────────────────────────────────────

    /**
     * Resolve the cached file path. Returns null if file does not exist.
     */
    public function resolve(string $locale, string $device): ?string
    {
        $path = $this->path($locale, $device);
        return File::exists($path) ? $path : null;
    }

    /**
     * Age of the cached file in seconds. Returns PHP_INT_MAX if missing.
     */
    public function age(string $locale, string $device): int
    {
        $path = $this->path($locale, $device);
        if (!File::exists($path)) return PHP_INT_MAX;
        return time() - File::lastModified($path);
    }

    /**
     * Is the cached file older than STALE_AFTER seconds?
     */
    public function isStale(string $locale, string $device): bool
    {
        return $this->age($locale, $device) > self::STALE_AFTER;
    }

    // ─── Capture from live response ────────────────────────────────

    /**
     * Capture a live Laravel response and save it as the static cache.
     * Called by middleware on MISS — the first visitor's response seeds the cache.
     */
    public function storeFromResponse(Response $response, string $locale, string $device): void
    {
        if ($response->getStatusCode() !== 200) return;

        $html = $response->getContent();
        if (empty($html) || !str_contains($html, '</html>')) return;

        $html = $this->injectStaticMarkers($html, $locale, $device);
        $this->write($locale, $device, $html);
    }

    // ─── Background regeneration (after response sent) ─────────────

    /**
     * Regenerate a single variant via HTTP self-call.
     * Meant to be called from app()->terminating() — runs after response is sent.
     */
    public function regenerateInBackground(string $locale, string $device): void
    {
        if (!$this->acquireLock($locale, $device)) return;

        try {
            $url = rtrim(config('app.url'), '/') . '/';

            $userAgent = $device === 'mobile'
                ? 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15'
                : 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0';

            $response = Http::withHeaders([
                'X-Static-Render' => $this->bypassToken,
                'User-Agent'      => $userAgent,
                'Accept-Language'  => $locale,
            ])->timeout(30)->get($url);

            if ($response->successful()) {
                $html = $this->injectStaticMarkers($response->body(), $locale, $device);
                $this->write($locale, $device, $html);
                Log::info("StaticPageRenderer: regenerated [{$locale}/{$device}]");
            } else {
                Log::warning("StaticPageRenderer: regen failed [{$locale}/{$device}] — HTTP {$response->status()}");
            }
        } catch (\Throwable $e) {
            Log::error("StaticPageRenderer: regen exception [{$locale}/{$device}] — {$e->getMessage()}");
        } finally {
            $this->releaseLock($locale, $device);
        }
    }

    /**
     * Render all variants (used by queue job & artisan command).
     */
    public function renderHomepage(): void
    {
        $locale = config('app.website_locale', 'ar');
        foreach (['desktop', 'mobile'] as $device) {
            $this->regenerateInBackground($locale, $device);
        }
    }

    // ─── Lock mechanism (prevents thundering herd) ─────────────────

    private function lockPath(string $locale, string $device): string
    {
        return $this->cachePath . "/.lock_{$locale}_{$device}";
    }

    public function acquireLock(string $locale, string $device): bool
    {
        $lockFile = $this->lockPath($locale, $device);
        $this->ensureDirectory();

        // If lock exists and is recent, someone else is regenerating
        if (File::exists($lockFile)) {
            $lockAge = time() - File::lastModified($lockFile);
            if ($lockAge < self::LOCK_TIMEOUT) return false;
            // Stale lock — take over
        }

        File::put($lockFile, (string) time());
        return true;
    }

    public function releaseLock(string $locale, string $device): void
    {
        $lockFile = $this->lockPath($locale, $device);
        if (File::exists($lockFile)) {
            File::delete($lockFile);
        }
    }

    // ─── Purge ─────────────────────────────────────────────────────

    public function purge(): void
    {
        if (File::isDirectory($this->cachePath)) {
            foreach (File::files($this->cachePath) as $file) {
                $name = $file->getFilename();
                if (str_starts_with($name, 'home_') || str_starts_with($name, '.lock_')) {
                    File::delete($file->getPathname());
                }
            }
        }
        Log::info('StaticPageRenderer: all homepage variants purged');
    }

    // ─── Internals ─────────────────────────────────────────────────

    private function injectStaticMarkers(string $html, string $locale, string $device): string
    {
        $timestamp = now()->toIso8601String();

        // Meta tag for client-side static detection
        $html = preg_replace(
            '/<head(\s[^>]*)?>/',
            '$0' . "\n" . '<meta name="x-static-page" content="' . $timestamp . '"/>',
            $html,
            1
        );

        // HTML comment for debugging
        $html = str_replace(
            '</html>',
            "<!-- static-render: {$timestamp} | {$locale} | {$device} -->\n</html>",
            $html
        );

        return $html;
    }

    private function write(string $locale, string $device, string $html): void
    {
        $this->ensureDirectory();
        // Atomic write: write to temp file then rename (prevents serving half-written files)
        $target = $this->path($locale, $device);
        $tmp = $target . '.tmp.' . getmypid();
        File::put($tmp, $html);
        rename($tmp, $target);
    }

    private function ensureDirectory(): void
    {
        if (!File::isDirectory($this->cachePath)) {
            File::makeDirectory($this->cachePath, 0755, true);
        }
    }

    private function path(string $locale, string $device): string
    {
        return $this->cachePath . "/home_{$locale}_{$device}.html";
    }

    public function getBypassToken(): string
    {
        return $this->bypassToken;
    }
}
