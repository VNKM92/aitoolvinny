<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Http\Request;

$app->instance('request', Request::create('/', 'GET'));
app()->setLocale('en');
$locale = 'en';

$postService = $app->make(\App\Services\PostService::class);
$seoService = $app->make(\App\Services\SEOService::class);
$slug = 'top-10-developer-tips-for-2026-part-1';

$post = $postService->getPostBySlug($slug);
$pages = $app->make(\App\Services\PageService::class)->getPublishedPages();
$seo = $seoService->generateTags($post, $locale);
$jsonLd = $seoService->generateJsonLd($post, $locale);

// EXACT same pageOverrides as post.blade.php passes
$pageOverrides = [
    'theme_body_bg' => $post->theme_body_bg,   // all these are NULL
    'theme_body_text' => $post->theme_body_text,
    'theme_header_bg' => $post->theme_header_bg,
    'theme_footer_bg' => $post->theme_footer_bg,
    'theme_primary' => $post->theme_primary,
    'theme_accent' => $post->theme_accent,
    'theme_section_bg' => $post->theme_section_bg,
    'theme_card_bg' => $post->theme_card_bg,
];

echo "=== Testing Tenant-Layout with explicit pageOverrides (post.blade.php pattern) ===\n";
echo "pageOverrides values: " . json_encode($pageOverrides) . "\n\n";

try {
    $html = view('components.tenant-layout', [
        'locale' => $locale,
        'pages' => $pages,
        'seo' => $seo,
        'jsonLd' => $jsonLd,
        'page' => null,
        'post' => null,   // note: post.blade doesn't pass :post attribute!
        'bodyClasses' => '',
        'pageOverrides' => $pageOverrides,
        'slot' => '',    // empty slot, just test the layout shell
    ])->render();
    echo "SUCCESS: Layout rendered OK (" . strlen($html) . " bytes)\n";
} catch (\Throwable $e) {
    echo "❌ ERROR: " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo "   at " . basename($e->getFile()) . ":" . $e->getLine() . "\n";
    echo "   TRACE (top 10):\n";
    $i = 1;
    foreach ($e->getTrace() as $t) {
        echo "     #$i " . ($t['file'] ?? '?') . ":" . ($t['line'] ?? '?') . " ";
        if (isset($t['class'])) echo $t['class'] . ($t['type'] ?? '::');
        if (isset($t['function'])) echo $t['function'] . "()";
        echo "\n";
        if ($i++ >= 10) break;
    }
    $prev = $e->getPrevious();
    while ($prev) {
        echo "   PREV: " . get_class($prev) . ": " . $prev->getMessage() . "\n";
        echo "     at " . basename($prev->getFile()) . ":" . $prev->getLine() . "\n";
        $prev = $prev->getPrevious();
    }
}

echo "\n=== Now Testing Tenant-Layout with NO pageOverrides (home/page.blade pattern) ===\n";
echo "(pageOverrides = NULL, so layout auto-detects; also pass :post attribute like page does)\n\n";

try {
    $html = view('components.tenant-layout', [
        'locale' => $locale,
        'pages' => $pages,
        'seo' => $seo,
        'jsonLd' => $jsonLd,
        'page' => null,
        'post' => $post,   // pass the post!
        'bodyClasses' => '',
        'pageOverrides' => null,
        'slot' => '',
    ])->render();
    echo "SUCCESS: Layout rendered OK (" . strlen($html) . " bytes)\n";
} catch (\Throwable $e) {
    echo "❌ ERROR: " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo "   at " . basename($e->getFile()) . ":" . $e->getLine() . "\n";
    $prev = $e->getPrevious();
    while ($prev) {
        echo "   PREV: " . get_class($prev) . ": " . $prev->getMessage() . "\n";
        echo "     at " . basename($prev->getFile()) . ":" . $prev->getLine() . "\n";
        $prev = $prev->getPrevious();
    }
}
