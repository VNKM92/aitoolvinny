<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Tenants;
use App\Livewire\Admin\Categories;
use App\Livewire\Admin\Posts;
use App\Livewire\Admin\Pages;
use App\Livewire\Admin\Settings;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Auth;

// 1. Central SaaS App & Super Admin routes (restricted to central domains)
$centralDomains = config('tenancy.central_domains', ['localhost', '127.0.0.1', 'central.local', 'aitoolvinny.local']);

foreach ($centralDomains as $domain) {
    Route::domain($domain)->group(function () {
        Route::get('/', function () {
            return view('central_welcome');
        })->name('central.welcome');

        Route::middleware(['guest'])->group(function () {
            Route::get('/login', Login::class)->name('login');
        });

        Route::middleware(['auth'])->group(function () {
            Route::get('/admin', Dashboard::class)->name('admin.dashboard');
            
            // Super Admin only routes
            Route::middleware([\App\Http\Middleware\EnsureSuperAdmin::class])->group(function () {
                Route::get('/admin/websites', Tenants::class)->name('admin.tenants');
            });

            // Logout
            Route::get('/logout', function () {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                return redirect()->route('login');
            })->name('admin.logout');
        });
    });
}

// -------------------------------------------------------------
// 2. Tenant Frontend & Backend routes (matched on all other domains)
// -------------------------------------------------------------

// Tenant admin panel routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', Dashboard::class)->name('admin.dashboard');
    Route::get('/admin/categories', Categories::class)->name('admin.categories');
    Route::get('/admin/posts', Posts::class)->name('admin.posts');
    Route::get('/admin/pages', Pages::class)->name('admin.pages');
    Route::get('/admin/settings', Settings::class)->name('admin.settings');

    Route::get('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('admin.logout');
});

// Dynamic XML Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('tenant.sitemap');

// Prefixed Tenant Public Routing (e.g. site.com/es/...)
Route::prefix('{locale}')->middleware([\App\Http\Middleware\SetLocale::class])->group(function () {
    Route::get('/', [TenantController::class, 'home'])->name('tenant.home.locale');
    Route::get('/posts/{slug}', [TenantController::class, 'post'])->name('tenant.post.locale');
    Route::get('/pages/{slug}', [TenantController::class, 'page'])->name('tenant.page.locale');
    Route::get('/categories/{slug}', [TenantController::class, 'category'])->name('tenant.category.locale');
});

// Unprefixed Tenant Public Routing (e.g. site.com/...)
Route::middleware([\App\Http\Middleware\SetLocale::class])->group(function () {
    Route::get('/', [TenantController::class, 'home'])->name('tenant.home');
    Route::get('/posts/{slug}', [TenantController::class, 'post'])->name('tenant.post');
    Route::get('/pages/{slug}', [TenantController::class, 'page'])->name('tenant.page');
    Route::get('/categories/{slug}', [TenantController::class, 'category'])->name('tenant.category');
});
