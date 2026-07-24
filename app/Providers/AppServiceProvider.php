<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TenantManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantManager::class, function ($app) {
            return new TenantManager();
        });

        $this->app->bind(
            \App\Repositories\Contracts\TenantRepositoryInterface::class,
            \App\Repositories\Eloquent\TenantRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\CategoryRepositoryInterface::class,
            \App\Repositories\Eloquent\CategoryRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PostRepositoryInterface::class,
            \App\Repositories\Eloquent\PostRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PageRepositoryInterface::class,
            \App\Repositories\Eloquent\PageRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
