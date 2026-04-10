<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */

    // public function boot(): void
    // {
    //     // Determine the active language
    //     $lang = $this->resolveActiveLanguage();

    //     // Load default translations
    //     $defaultValues = $this->getDefaultTranslations($lang);

    //     // Load and merge settings
    //     $this->loadSystemSettings($defaultValues, $lang);
    // }

//     protected function resolveActiveLanguage(): string
// {
//     if (app()->has('current_language')) {
//         return app('current_language');
//     }

//     $runtimeLocale = app()->getLocale();
//     if ($runtimeLocale && $runtimeLocale !== config('app.locale')) {
//         return $runtimeLocale;
//     }

//     $request = request();
//     if ($request->is('dashboard*')) {
//         return $runtimeLocale ?: config('app.locale');
//     }

//     return config('app.website_locale');
// }
    // protected function getDefaultTranslations(string $lang): array
    // {
    //     return [
    //         'site_name' => __('messages.SettingsDefault.site_name', [], $lang),
    //         'light_site_name' => __('messages.SettingsDefault.light_site_name', [], $lang),
    //         'footer_description' => __('messages.SettingsDefault.footer_description', [], $lang),
    //         'site_description' => __('messages.SettingsDefault.site_description', [], $lang),
    //         'copyright_text' => __('messages.SettingsDefault.copyright_text', ['year' => now()->year], $lang),
    //         'water_mark_place' => __('messages.SettingsDefault.water_mark_place', [], $lang),
    //     ];
    // }
    // protected function loadSystemSettings(array $defaultValues, string $lang): void
    // {
    //     $setting = Setting::with('files.file')->first();

    //     // Basic Site Settings
    //     Config::set('system.site_name', $defaultValues['site_name']);
    //     Config::set('system.light_site_name', $setting?->light_site_name ?: $defaultValues['light_site_name']);
    //     Config::set('system.main_news_background', $setting?->main_news_background);
    //     Config::set('system.footer_description', $setting?->footer_description ?: $defaultValues['footer_description']);
    //     Config::set('system.site_description', $setting?->site_description ?: $defaultValues['site_description']);
    //     Config::set('system.copyright_text', $setting?->copyright_text ?: $defaultValues['copyright_text']);
    //     Config::set('system.website_active', $setting?->website_active ?? true);

    //     // Media Files
    //     Config::set('system.favicon', $setting?->files->where('model_column', 'favicon')->first()?->file?->path);
    //     Config::set('system.water_mark_image', $setting?->water_mark_image ?? null);
    //     Config::set('system.water_mark_place', $setting?->water_mark_place ?: $defaultValues['water_mark_place']);

    //     // Contact Information
    //     Config::set('system.site_email', $setting?->site_email ?? 'example@example.com');
    //     Config::set('system.address', $setting?->address ?? '');
    //     Config::set('system.phone', $setting?->phone ?? '');

    //     // Open Graph/Meta Settings
    //     Config::set('system.tags', $setting?->tags ?? '');
    //     Config::set('system.open_graph_image', $setting?->open_graph_image ?? null);
    //     Config::set('system.open_graph_description', $setting?->open_graph_description ?? '');
    //     Config::set('system.open_graph_local', $setting?->open_graph_local ?? 'en_US');
    //     Config::set('system.open_graph_author', $setting?->open_graph_author ?? '');
    //     Config::set('system.open_graph_twitter_creator', $setting?->open_graph_twitter_creator ?? '');
    //     Config::set('system.open_graph_twitter_site', $setting?->open_graph_twitter_site ?? '');

    //     // Custom Code Injection
    //     Config::set('system.header_code', $setting?->header_code ?? '');
    //     Config::set('system.footer_code', $setting?->footer_code ?? '');

    //     // Logo Handling
    //     foreach ($setting?->files as $item) {
    //         switch ($item?->model_column) {
    //             case 'site_logo':
    //                 Config::set('system.site_logo', $item?->file?->path);
    //                 break;
    //             case 'footer_logo':
    //                 Config::set('system.footer_logo', $item?->file?->path);
    //                 break;
    //         }
    //     }

    //     // Special Hodhod Case
    //     if (config('app.launch') === 'hodhod') {
    //         $hodhodBanner = $setting->files->firstWhere('model_column', 'hodhod_banner')?->file?->path;
    //         if ($hodhodBanner) {
    //             Config::set('system.hodhod_banner', $hodhodBanner);
    //         }
    //     }
    // }
}
