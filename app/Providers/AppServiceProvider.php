<?php

namespace App\Providers;

use App\Services\PabrikConfigService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Saat di shared hosting Hostinger (public dipisah ke sibling ../public_html), arahkan public_path otomatis
        $siblingPublicHtml = dirname(base_path()) . DIRECTORY_SEPARATOR . 'public_html';
        if (!is_dir(base_path('public')) && is_dir($siblingPublicHtml)) {
            $this->app->usePublicPath($siblingPublicHtml);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.simple-custom');

        View::composer('*', function ($view) {
            $view->with([
                'pabrikWa' => PabrikConfigService::waPabrik(),
                'pabrikWaDisplay' => PabrikConfigService::waPabrikDisplay(),
                'pabrikNppbkc' => PabrikConfigService::nppbkc(),
                'pabrikJamOperasional' => PabrikConfigService::jamOperasional(),
            ]);
        });
    }
}
