<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Http\Request;

$app->instance('request', Request::create('/', 'GET'));

// Test page view
echo "=== Testing Page view (page.blade.php) ===" . PHP_EOL;
try {
    $page = \App\Models\Page::where('status', 'published')->first();
    if (!$page) {
        echo "No published pages found, creating dummy...\n";
        // Create temp page
        $page = new \App\Models\Page();
        $page->title = ['en' => 'Test Page', 'es' => 'Página de Prueba'];
        $page->slug = 'test-page';
        $page->content = ['en' => '<p>Hello World</p>', 'es' => '<p>Hola Mundo</p>'];
        $page->excerpt = ['en' => 'Test excerpt', 'es' => 'Extracto'];
        $page->meta_title = ['en' => 'Test', 'es' => 'Prueba'];
        $page->meta_description = ['en' => 'Test', 'es' => 'Prueba'];
        $page->status = 'published';
    }
    echo "Found page: {$page->title['en']}" . PHP_EOL;
    
    $controller = $app->make(\App\Http\Controllers\TenantController::class);
    $response = $controller->page($page->slug, null);
    $rendered = $response->render();
    echo "SUCCESS: Page rendered OK (" . strlen($rendered) . " bytes)" . PHP_EOL;
} catch (\Throwable $e) {
    echo "PAGE ERROR: " . get_class($e) . ": " . $e->getMessage() . PHP_EOL;
    echo "  at: " . basename($e->getFile()) . ":" . $e->getLine() . PHP_EOL;
}

echo PHP_EOL . "=== Testing Post view WITHOUT explicit pageOverrides ===" . PHP_EOL;
echo "(Simulating the post view by modifying post.blade.php to not pass pageOverrides...)" . PHP_EOL;

// Quick test: pass pageOverrides as EMPTY array (null would trigger auto)
$postService = $app->make(\App\Services\PostService::class);
$seoService = $app->make(\App\Services\SEOService::class);
app()->setLocale('en');
$locale = 'en';

$slug = 'top-10-developer-tips-for-2026-part-1';
$post = $postService->getPostBySlug($slug);
$post->loadMissing(['category', 'subcategory', 'tags', 'comments']);
$pages = $app->make(\App\Services\PageService::class)->getPublishedPages();
$categories = \App\Models\Category::with('subcategories')->orderBy('id', 'asc')->get();
$relatedPosts = $postService->getRelatedPosts($post, 6);
$latestPosts = $postService->getLatestPublished(5);
$featuredPosts = $postService->getFeaturedPublished(4);
$seo = $seoService->generateTags($post, $locale);
$jsonLd = $seoService->generateJsonLd($post, $locale);

// Now try rendering with different pageOverrides to see which causes the error
echo PHP_EOL . "Test 1: pageOverrides = NULL (auto detect, like home page)" . PHP_EOL;
try {
    $rendered = view('tenant.post', array_merge(
        compact('post', 'pages', 'categories', 'relatedPosts', 'latestPosts', 'featuredPosts', 'seo', 'jsonLd', 'locale'),
        []  // no explicit pageOverrides passed, so it's not in the view
    ))->render();
    echo "  SUCCESS with auto pageOverrides! (" . strlen($rendered) . " bytes)" . PHP_EOL;
} catch (\Throwable $e) {
    echo "  ERROR: " . $e->getMessage() . PHP_EOL;
    echo "    at: " . basename($e->getFile()) . ":" . $e->getLine() . PHP_EOL;
    // Now unwrap
    $prev = $e->getPrevious();
    while ($prev) {
        echo "  PREVIOUS: " . get_class($prev) . ": " . $prev->getMessage() . PHP_EOL;
        echo "    at: " . basename($prev->getFile()) . ":" . $prev->getLine() . PHP_EOL;
        $prev = $prev->getPrevious();
    }
}
