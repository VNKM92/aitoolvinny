<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Http\Request;

$app->instance('request', Request::create('/', 'GET'));

$postService = $app->make(\App\Services\PostService::class);
$pageService = $app->make(\App\Services\PageService::class);
$seoService = $app->make(\App\Services\SEOService::class);
$locale = 'en';
app()->setLocale($locale);

$slug = 'top-10-developer-tips-for-2026-part-1';
$post = $postService->getPostBySlug($slug);
$post->loadMissing(['category', 'subcategory', 'tags', 'comments']);

echo "=== Checking SEO array ===" . PHP_EOL;
$seo = $seoService->generateTags($post, $locale);
$jsonLd = $seoService->generateJsonLd($post, $locale);

array_walk_recursive($seo, function($val, $key) {
    if (is_array($val)) {
        echo "  ERROR: seo.$key is an ARRAY: " . json_encode($val) . PHP_EOL;
    } elseif (is_object($val)) {
        echo "  WARNING: seo.$key is an OBJECT: " . get_class($val) . PHP_EOL;
    }
});
echo "  seo OK - all leaf values are primitives" . PHP_EOL;

echo PHP_EOL . "=== Checking jsonLd ===" . PHP_EOL;
if (is_array($jsonLd)) {
    echo "  ERROR: jsonLd is an ARRAY (expected string)" . PHP_EOL;
    echo "  Keys: " . implode(',', array_keys($jsonLd)) . PHP_EOL;
} else {
    echo "  jsonLd OK - is a " . gettype($jsonLd) . " of length " . strlen($jsonLd) . PHP_EOL;
}

echo PHP_EOL . "=== Checking theme overrides ===" . PHP_EOL;
$pageOverrides = [
    'theme_body_bg' => $post->theme_body_bg,
    'theme_body_text' => $post->theme_body_text,
    'theme_header_bg' => $post->theme_header_bg,
    'theme_footer_bg' => $post->theme_footer_bg,
    'theme_primary' => $post->theme_primary,
    'theme_accent' => $post->theme_accent,
    'theme_section_bg' => $post->theme_section_bg,
    'theme_card_bg' => $post->theme_card_bg,
];
foreach ($pageOverrides as $k => $v) {
    $t = gettype($v);
    if ($t === 'array') {
        echo "  ERROR: $k is an ARRAY!" . PHP_EOL;
    } elseif ($t === 'object') {
        echo "  ERROR: $k is an OBJECT: " . get_class($v) . PHP_EOL;
    } elseif ($t === 'NULL') {
        echo "  info: $k is NULL" . PHP_EOL;
    } else {
        echo "  ok: $k is $t = $v" . PHP_EOL;
    }
}

echo PHP_EOL . "=== Checking effectiveSettings (ThemeService) ===" . PHP_EOL;
$eff = \App\Services\ThemeService::getEffectiveThemeSettings($pageOverrides, false);
$count = 0;
foreach ($eff as $k => $v) {
    if (is_array($v)) {
        echo "  ERROR: effectiveSettings.$k is an ARRAY!" . PHP_EOL;
        echo "    Keys: " . implode(',', array_keys($v)) . PHP_EOL;
        $count++;
    } elseif (is_object($v)) {
        echo "  ERROR: effectiveSettings.$k is an OBJECT: " . get_class($v) . PHP_EOL;
        $count++;
    }
}
if ($count === 0) {
    echo "  All " . count($eff) . " settings are scalar primitives (OK)" . PHP_EOL;
}

echo PHP_EOL . "=== Now testing post.blade.php vars by simulating each echo ===" . PHP_EOL;
echo "--- title[$locale] = " . (is_array($post->title[$locale]) ? 'ARRAY!' : gettype($post->title[$locale])) . PHP_EOL;
echo "--- reset(title) = " . (is_array(reset($post->title)) ? 'ARRAY!' : gettype(reset($post->title))) . PHP_EOL;

echo PHP_EOL . "=== Checking category->name ===" . PHP_EOL;
if ($post->category) {
    echo "  category->name is " . gettype($post->category->name);
    if (is_array($post->category->name)) {
        echo " keys=[" . implode(',', array_keys($post->category->name)) . "]";
        echo " first is " . gettype(reset($post->category->name));
    }
    echo PHP_EOL;
    $val = $post->category->name[$locale] ?? reset($post->category->name);
    echo "  category->name[loc] ?? reset = type=" . gettype($val) . " val=$val" . PHP_EOL;
}

echo PHP_EOL . "=== THE ULTIMATE TEST: Actually echo all values from post view ===" . PHP_EOL;
$error_found = false;

$testExpressions = [
    'seo_title' => fn() => $seo['title'],
    'seo_description' => fn() => $seo['description'],
    'seo_canonical' => fn() => $seo['canonical'],
    'seo_og_title' => fn() => $seo['og']['title'],
    'seo_og_desc' => fn() => $seo['og']['description'],
    'seo_og_url' => fn() => $seo['og']['url'],
    'seo_twitter_card' => fn() => $seo['twitter']['card'],
    'post_title_keyed' => fn() => $post->title[$locale] ?? reset($post->title),
    'post_excerpt_accessor' => fn() => $post->excerpt,  // accessor now returns string
    'post_excerptText' => fn() => $post->excerptText(),
    'post_featured_image' => fn() => $post->featured_image,
    'post_category_name' => fn() => $post->category ? ($post->category->name[$locale] ?? reset($post->category->name)) : '',
    'post_subcategory_name' => fn() => $post->subcategory ? ($post->subcategory->name[$locale] ?? reset($post->subcategory->name)) : '',
];

foreach ($testExpressions as $name => $fn) {
    try {
        $val = $fn();
        if (is_array($val)) {
            echo "  ERROR: $name returned ARRAY keys=[" . implode(',', array_keys($val)) . "]" . PHP_EOL;
            $error_found = true;
        } elseif (is_object($val)) {
            echo "  ERROR: $name returned OBJECT: " . get_class($val) . PHP_EOL;
            $error_found = true;
        } else {
            if (strlen((string)$val) < 50) {
                echo "  OK: $name = " . var_export($val, true) . PHP_EOL;
            } else {
                echo "  OK: $name = (" . strlen($val) . " bytes)" . PHP_EOL;
            }
        }
    } catch (\Throwable $e) {
        echo "  EXCEPTION in $name: " . $e->getMessage() . PHP_EOL;
        $error_found = true;
    }
}

echo PHP_EOL . "Summary: " . ($error_found ? "ERRORS FOUND!" : "All values are clean scalars, hmmm...") . PHP_EOL;
