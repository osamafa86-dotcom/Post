<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('dashboard*')) {
            $locale = session()->get('locale') ?? $this->getDashboardLocale($request);
        } else {
            // FiMED: read language from URL prefix /{lang}/...
            $langSegment = $request->segment(1);
            $enabledFrontend = config('app.enabled_languages', ['ar', 'en']);
            if ($langSegment && in_array($langSegment, $enabledFrontend)) {
                $locale = $langSegment;
                session(['locale' => $locale]);
            } else {
                $locale = session()->get('locale') ?? config('app.website_locale');
            }
        }

        // Ensure only enabled languages can be set
        $enabledLanguages = config('app.enabled_languages', ['ar', 'en']);
        if (!in_array($locale, $enabledLanguages)) {
            $locale = config('app.fallback_locale', 'ar');
        }

        app()->setLocale($locale);
        config(['app.locale' => $locale]);
        $this->loadSystemSettings($locale);

        return $next($request);
    }

    protected function getDashboardLocale(Request $request): string
    {
        // Default dashboard locale
        $defaultLocale = config('app.locale');

        // For authenticated dashboard users
        if (auth()->check()) {
            $user = auth()->user();

            // Update user preference if session locale exists
            if (session()->has('locale')) {
                $user->update(['default_dashboard_language' => $defaultLocale]);
                return session('locale');
            }

            return $user->default_dashboard_language ?? $defaultLocale;
        }

        return $defaultLocale;

    }

    protected function loadSystemSettings(string $locale): void
    {
        $defaultValues = [
            'site_name' => __('messages.SettingsDefault.site_name', [], $locale),
            'light_site_name' => __('messages.SettingsDefault.light_site_name', [], $locale),
            'footer_description' => __('messages.SettingsDefault.footer_description', [], $locale),
            'site_description' => __('messages.SettingsDefault.site_description', [], $locale),
            'copyright_text' => __('messages.SettingsDefault.copyright_text', ['year' => now()->year], $locale),
            'water_mark_place' => __('messages.SettingsDefault.water_mark_place', [], $locale),
        ];

        $setting = Setting::withoutGlobalScopes()->with('files.file')->first();

        Config::set('system.site_name', $setting?->site_name ?: $defaultValues['site_name']);
        Config::set('system.light_site_name', $setting?->light_site_name ?: $defaultValues['light_site_name']);
        Config::set('system.main_news_background', $setting?->main_news_background);
        Config::set('system.footer_description', $setting?->footer_description ?: $defaultValues['footer_description']);
        Config::set('system.site_description', $setting?->site_description ?: $defaultValues['site_description']);
        Config::set('system.copyright_text', $setting?->copyright_text ?: $defaultValues['copyright_text']);
        Config::set('system.website_active', $setting?->website_active ?? true);

        Config::set('system.favicon', $setting?->files->where('model_column', 'favicon')->first()?->file?->path);
        Config::set('system.water_mark_image', $setting?->water_mark_image ?? null);
        Config::set('system.water_mark_place', $setting?->water_mark_place ?: $defaultValues['water_mark_place']);

        Config::set('system.site_email', $setting?->site_email ?? '');
        Config::set('system.address', $setting?->address ?? '');
        Config::set('system.phone', $setting?->phone ?? '');

        Config::set('system.tags', $setting?->tags ?? '');
        Config::set('system.open_graph_image', $setting?->open_graph_image ?? null);
        Config::set('system.open_graph_description', $setting?->open_graph_description ?? '');
        Config::set('system.open_graph_local', $setting?->open_graph_local ?? 'en_US');
        Config::set('system.open_graph_author', $setting?->open_graph_author ?? '');
        Config::set('system.open_graph_twitter_creator', $setting?->open_graph_twitter_creator ?? '');
        Config::set('system.open_graph_twitter_site', $setting?->open_graph_twitter_site ?? '');

        Config::set('system.header_code', $setting?->header_code ?? '');
        Config::set('system.footer_code', $setting?->footer_code ?? '');
        if($setting){

        foreach ($setting?->files as $item) {
            switch ($item?->model_column) {
                case 'site_logo':
                    Config::set('system.site_logo', $item?->file?->path);
                    break;
                case 'footer_logo':
                    Config::set('system.footer_logo', $item?->file?->path);
                    break;
            }
        }
        }

        if (config('features.general.has_banner')) {
            $hodhodBanner = $setting?->files?->firstWhere('model_column', 'hodhod_banner')?->file?->path;
            if ($hodhodBanner) {
                Config::set('system.hodhod_banner', $hodhodBanner);
            }
        }
        if (config('features.general.has_podcast_url')) {
            Config::set('system.podcast_url', $setting?->podcast_url ?? '');
        }
        if (config('features.general.has_claim_url')) {
            Config::set('system.claim_url', $setting?->claim_url ?? '');
        }
        if (config('features.general.has_whatsapp_link')) {
            Config::set('system.whatsapp_link', $setting?->whatsapp_link ?? '');
        }
        if (config('features.general.has_landing_page')) {
            Config::set('system.landing_page_url', $setting?->landing_page_url ?? '');
            $landingPageInformationPicturesTopRight = $setting?->files?->firstWhere('model_column', 'landingPageInformationPicturesTopRight')?->file?->path;
            $landingPageInformationPicturesTopLeft = $setting?->files?->firstWhere('model_column', 'landingPageInformationPicturesTopLeft')?->file?->path;
            $landingPageInformationPicturesBottomRight = $setting?->files?->firstWhere('model_column', 'landingPageInformationPicturesBottomRight')?->file?->path;
            $landingPageInformationPicturesBottomLeft = $setting?->files?->firstWhere('model_column', 'landingPageInformationPicturesBottomLeft')?->file?->path;
            if ($landingPageInformationPicturesTopRight) {
                Config::set('system.landingPageInformationPicturesTopRight', $landingPageInformationPicturesTopRight);
            }
            if ($landingPageInformationPicturesTopLeft) {
                Config::set('system.landingPageInformationPicturesTopLeft', $landingPageInformationPicturesTopLeft);
            }
            if ($landingPageInformationPicturesBottomRight) {
                Config::set('system.landingPageInformationPicturesBottomRight', $landingPageInformationPicturesBottomRight);
            }
            if ($landingPageInformationPicturesBottomLeft) {
                Config::set('system.landingPageInformationPicturesBottomLeft', $landingPageInformationPicturesBottomLeft);
            }
        }
    }
}
