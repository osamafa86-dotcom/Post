<?php

namespace App\Providers;

use App\Models\Post;
use App\Observers\PostCacheObserver;
use App\Services\Payment\Razorpay\RazorpayPaymentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Post observer for automatic cache clearing
        Post::observe(PostCacheObserver::class);

        $this->app->bind('App\Services\Payment\Contract\PaymentGatewayContract', function () {
            return new RazorpayPaymentService();
        });

        // --- Performance & Safety Guards (dev only) ---
        if ($this->app->environment('local', 'staging')) {
            // Detect N+1 queries: logs a warning instead of throwing exception
            Model::preventLazyLoading(!$this->app->isProduction());
            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                $class = get_class($model);
                logger()->warning("N+1 Query: {$class}::{$relation} lazy loaded");
            });
        }

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            // Prevent destructive commands in production
            DB::prohibitDestructiveCommands();
        }

        if (Auth::check()) {
            App::setLocale(Auth::user()->default_dashboard_language ?? 'ar');
        }
        if (file_exists(config_path('features.php'))) {
            config(['features' => require config_path('features.php')]);
        }

        // Compute enabled languages: intersection of app.languages (globally enabled)
        // and features.frontend_languages (per-project enabled)
        $globalLanguages = collect(config('app.languages'))
            ->filter(fn($lang) => $lang['enabled'])
            ->keys()
            ->toArray();

        $projectLanguages = collect(config('features.frontend_languages', []))
            ->filter(fn($enabled) => $enabled)
            ->keys()
            ->toArray();

        // Frontend: only languages enabled in BOTH app.languages AND features.frontend_languages
        $enabledLanguages = !empty($projectLanguages)
            ? array_values(array_intersect($globalLanguages, $projectLanguages))
            : $globalLanguages;

        config(['app.enabled_languages' => $enabledLanguages]);

        // Override default locale from features if set
        $defaultFrontendLang = config('features.default_frontend_language');
        if ($defaultFrontendLang && in_array($defaultFrontendLang, $enabledLanguages)) {
            config(['app.website_locale' => $defaultFrontendLang]);
        }

        // Livewire Blaze is installed and available.
        // Use @blaze directive in individual simple anonymous components
        // (icons, buttons, cards) for optimized rendering.
        // Layouts and modals are too complex (DB queries, global state) for Blaze::optimize().
    }
}
