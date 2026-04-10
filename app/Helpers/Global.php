<?php

use App\Helpers\FileHelper;

if (!function_exists('file_url')) {
    function file_url(?string $path): ?string
    {
        return FileHelper::fileUrl($path);
    }
}

/**
 * Generate a frontend route with automatic lang prefix for projects that need it.
 * Usage: frontend_route('show_post', ['id' => 5, 'slug' => 'test'])
 *        frontend_route('index')
 *        frontend_route('share', ['id' => 5])
 */
if (!function_exists('frontend_route')) {
    function frontend_route(string $routeSuffix, array $params = []): string
    {
        $launch = config('app.launch');
        $routeName = "main.{$launch}.{$routeSuffix}";

        // Check if this route requires a 'lang' parameter
        $defaultLang = config('features.default_frontend_language');
        if ($defaultLang && !isset($params['lang'])) {
            $params['lang'] = $defaultLang;
        }

        try {
            return route($routeName, $params);
        } catch (\Exception $e) {
            // Fallback: try without lang (for projects that don't use lang prefix)
            unset($params['lang']);
            return route($routeName, $params);
        }
    }
}
