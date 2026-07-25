<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Categories;
use App\Livewire\Admin\Subcategories;
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
use App\Livewire\Admin\Monetization;
use App\Http\Controllers\MonetizationController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\Api\ToolsApiController;
use App\Livewire\Admin\ToolsManager;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NewsletterController;

// 1. Guest Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// 2. Auth Guarded CMS Control Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', Dashboard::class)->name('admin.dashboard');
    Route::get('/admin/categories', Categories::class)->name('admin.categories');
    Route::get('/admin/subcategories', Subcategories::class)->name('admin.subcategories');
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
    Route::get('/admin/monetization', Monetization::class)->name('admin.monetization');
    Route::get('/admin/tools', ToolsManager::class)->name('admin.tools');

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
Route::get('/manifest.json', function () {
    return response(file_get_contents(public_path('manifest.json')), 200, [
        'Content-Type' => 'application/json'
    ]);
});
Route::get('/sw.js', function () {
    return response(file_get_contents(public_path('sw.js')), 200, [
        'Content-Type' => 'application/javascript'
    ]);
});

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('tenant.newsletter.subscribe');
Route::get('/ad-click/{id}', [MonetizationController::class, 'adClick'])->name('tenant.ad.click');
Route::get('/go/{slug}', [MonetizationController::class, 'affiliateRedirect'])->name('tenant.affiliate.redirect');

// 20 free web tools API endpoints
Route::prefix('api/tools')->middleware('throttle:60,1')->group(function () {
    Route::post('/qr-code', [ToolsApiController::class, 'qrCode']);
    Route::post('/password', [ToolsApiController::class, 'password']);
    Route::post('/uuid', [ToolsApiController::class, 'uuid']);
    Route::post('/base64-encode', [ToolsApiController::class, 'base64Encode']);
    Route::post('/base64-decode', [ToolsApiController::class, 'base64Decode']);
    Route::post('/json-formatter', [ToolsApiController::class, 'jsonFormatter']);
    Route::post('/sql-formatter', [ToolsApiController::class, 'sqlFormatter']);
    Route::post('/html-formatter', [ToolsApiController::class, 'htmlFormatter']);
    Route::post('/css-minify', [ToolsApiController::class, 'cssMinifier']);
    Route::post('/js-beautify', [ToolsApiController::class, 'jsBeautifier']);
    Route::post('/word-counter', [ToolsApiController::class, 'wordCounter']);
    Route::post('/slug-generator', [ToolsApiController::class, 'slugGenerator']);
    Route::post('/lorem-ipsum', [ToolsApiController::class, 'loremIpsum']);
    Route::post('/age-calculator', [ToolsApiController::class, 'ageCalculator']);
    Route::post('/emi-calculator', [ToolsApiController::class, 'emiCalculator']);
    Route::post('/gst-calculator', [ToolsApiController::class, 'gstCalculator']);
    Route::post('/percentage-calculator', [ToolsApiController::class, 'percentageCalculator']);
    Route::post('/image-compress', [ToolsApiController::class, 'imageCompress']);
    Route::post('/character-counter', [ToolsApiController::class, 'characterCounter']);
    Route::post('/random-password', [ToolsApiController::class, 'randomPassword']);
});

Route::middleware([\App\Http\Middleware\SetLocale::class])->group(function () {
    Route::get('/tools', [ToolsController::class, 'index'])->name('tenant.tools.index');
    Route::get('/tools/{slug}', [ToolsController::class, 'show'])->name('tenant.tools.show');
});

// 4. Prefixed Localized Public Front Routing (e.g. site.com/es/...)
Route::prefix('{locale}')->middleware([\App\Http\Middleware\SetLocale::class])->group(function () {
    Route::get('/', [TenantController::class, 'home'])->name('tenant.home.locale');
    Route::get('/posts/{slug}', [TenantController::class, 'post'])->name('tenant.post.locale');
    Route::get('/pages/{slug}', [TenantController::class, 'page'])->name('tenant.page.locale');
    Route::get('/categories/{slug}', [TenantController::class, 'category'])->name('tenant.category.locale');
    Route::get('/subcategories/{slug}', [TenantController::class, 'subcategory'])->name('tenant.subcategory.locale');
    Route::get('/tools', [ToolsController::class, 'index'])->name('tenant.tools.index.locale');
    Route::get('/tools/{slug}', [ToolsController::class, 'show'])->name('tenant.tools.show.locale');
});

// 5. Unprefixed Default Localized Public Front Routing (e.g. site.com/...)
Route::middleware([\App\Http\Middleware\SetLocale::class])->group(function () {
    Route::get('/', [TenantController::class, 'home'])->name('tenant.home');
    Route::get('/posts/{slug}', [TenantController::class, 'post'])->name('tenant.post');
    Route::get('/pages/{slug}', [TenantController::class, 'page'])->name('tenant.page');
    Route::get('/categories/{slug}', [TenantController::class, 'category'])->name('tenant.category');
    Route::get('/subcategories/{slug}', [TenantController::class, 'subcategory'])->name('tenant.subcategory');
});

// 6. Fallback route to log 404s in middleware
Route::fallback(function () {
    abort(404);
});
