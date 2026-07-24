<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Categories;
use App\Livewire\Admin\Posts;
use App\Livewire\Admin\Pages;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\MediaManager;
use App\Livewire\Admin\Comments;
use App\Livewire\Admin\Newsletter;
use App\Livewire\Admin\Forms as AdminForms;
use App\Livewire\Admin\Faqs;
use App\Livewire\Admin\Popups;
use App\Livewire\Admin\ActivityLogs;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TenantController;
use App\Livewire\Admin\AIGenerator;
use Illuminate\Support\Facades\Auth;

// 1. Guest Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// 2. Auth Guarded CMS Control Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', Dashboard::class)->name('admin.dashboard');
    Route::get('/admin/categories', Categories::class)->name('admin.categories');
    Route::get('/admin/posts', Posts::class)->name('admin.posts');
    Route::get('/admin/pages', Pages::class)->name('admin.pages');
    Route::get('/admin/media', MediaManager::class)->name('admin.media');
    Route::get('/admin/comments', Comments::class)->name('admin.comments');
    Route::get('/admin/newsletter', Newsletter::class)->name('admin.newsletter');
    Route::get('/admin/forms', AdminForms::class)->name('admin.forms');
    Route::get('/admin/faqs', Faqs::class)->name('admin.faqs');
    Route::get('/admin/popups', Popups::class)->name('admin.popups');
    Route::get('/admin/logs', ActivityLogs::class)->name('admin.logs');
    Route::get('/admin/settings', Settings::class)->name('admin.settings');
    Route::get('/admin/ai', AIGenerator::class)->name('admin.ai');

    Route::get('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('admin.logout');
});

// 3. Dynamic XML Sitemap, Robots, and Feed Endpoints
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('tenant.sitemap');
Route::get('/sitemap-images.xml', [SitemapController::class, 'images'])->name('tenant.sitemap.images');
Route::get('/sitemap-news.xml', [SitemapController::class, 'news'])->name('tenant.sitemap.news');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('tenant.robots');
Route::get('/feed', [SitemapController::class, 'feed'])->name('tenant.feed');

// 4. Prefixed Localized Public Front Routing (e.g. site.com/es/...)
Route::prefix('{locale}')->middleware([\App\Http\Middleware\SetLocale::class])->group(function () {
    Route::get('/', [TenantController::class, 'home'])->name('tenant.home.locale');
    Route::get('/posts/{slug}', [TenantController::class, 'post'])->name('tenant.post.locale');
    Route::get('/pages/{slug}', [TenantController::class, 'page'])->name('tenant.page.locale');
    Route::get('/categories/{slug}', [TenantController::class, 'category'])->name('tenant.category.locale');
});

// 5. Unprefixed Default Localized Public Front Routing (e.g. site.com/...)
Route::middleware([\App\Http\Middleware\SetLocale::class])->group(function () {
    Route::get('/', [TenantController::class, 'home'])->name('tenant.home');
    Route::get('/posts/{slug}', [TenantController::class, 'post'])->name('tenant.post');
    Route::get('/pages/{slug}', [TenantController::class, 'page'])->name('tenant.page');
    Route::get('/categories/{slug}', [TenantController::class, 'category'])->name('tenant.category');
});

// 6. Fallback route to log 404s in middleware
Route::fallback(function () {
    abort(404);
});
