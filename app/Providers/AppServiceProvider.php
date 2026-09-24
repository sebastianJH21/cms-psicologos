<?php

namespace App\Providers;

use App\Services\ThemeManager;
use App\View\Composers\HeaderComposer;
use App\View\Composers\PublicLayoutComposer;
use App\View\Composers\SidebarComposer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeManager::class, fn () => new ThemeManager());
    }

    public function boot(): void
    {
        if (!$this->app->environment('local')) {
            $appUrl = (string) config('app.url');
            if ($appUrl !== '') {
                URL::forceRootUrl($appUrl);
                if (str_starts_with($appUrl, 'https://')) {
                    URL::forceScheme('https');
                }
            }
        }

        View::composer('dashboard.partials.sidebar', SidebarComposer::class);
        View::composer('dashboard.partials.header', HeaderComposer::class);

        Paginator::defaultView('vendor.pagination.dashboard');
        Paginator::defaultSimpleView('vendor.pagination.dashboard');

        $themeManager = $this->app->make(ThemeManager::class);
        $activeSlug = $themeManager->activeSlug();
        $viewsPath = $themeManager->viewsPath($activeSlug);

        $hasViews = File::isDirectory($viewsPath) && File::exists($viewsPath . '/landing.blade.php');
        if (!$hasViews) {
            $viewsPath = $themeManager->viewsPath('tema-base');
        }

        if (File::isDirectory($viewsPath)) {
            View::addNamespace('theme', $viewsPath);
        }

        View::composer('theme::*', PublicLayoutComposer::class);
    }
}
