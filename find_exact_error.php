<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Http\Request;

$app->instance('request', Request::create('/', 'GET'));
app()->setLocale('en');
$locale = 'en';

// Boot the app
$postService = $app->make(\App\Services\PostService::class);
$seoService = $app->make(\App\Services\SEOService::class);
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

echo "=== Testing all e() calls from post compiled view ===\n";

function testEcho($name, $value) {
    try {
        if (is_array($value)) {
            echo "  ❌ FAIL [$name]: array given: " . json_encode(array_keys($value)) . "\n";
            return false;
        } elseif (is_object($value) && !method_exists($value, '__toString')) {
            echo "  ❌ FAIL [$name]: object " . get_class($value) . " (no __toString)\n";
            return false;
        } elseif (is_object($value)) {
            $str = (string)$value;
            e($str);
            echo "  ✓ OK [$name]: object->__toString OK (" . strlen($str) . " bytes)\n";
            return true;
        } else {
            // Also test if it's null
            if ($value === null) {
                echo "  · OK [$name]: NULL (will be '')\n";
                return true;
            }
            e($value);
            echo "  ✓ OK [$name]: " . gettype($value) . " (" . (is_string($value) ? strlen($value) . " bytes" : $value) . ")\n";
            return true;
        }
    } catch (\Throwable $e) {
        echo "  ❌ FAIL [$name]: " . get_class($e) . ": " . $e->getMessage() . "\n";
        echo "    VALUE dump: " . var_export($value, true) . "\n";
        return false;
    }
}

// Now simulate ALL echo lines from the compiled post view
// Line 36: route('tenant.home', ['locale' => $locale])
testEcho('route tenant.home', route('tenant.home', ['locale' => $locale]));
// Line 40: route tenant.category
testEcho('route tenant.category (post cat)', route('tenant.category', ['slug' => $post->category->slug ?? 'none', 'locale' => $locale]));
// Line 42: category name
testEcho('post->category->name['.$locale.']', $post->category->name[$locale] ?? (is_array($post->category->name) ? array_values($post->category->name)[0] : ''));
// Line 49: route subcategory
testEcho('route tenant.subcategory', route('tenant.subcategory', ['slug' => $post->subcategory->slug ?? 'none', 'locale' => $locale]));
// Line 51: subcategory name
testEcho('post->subcategory->name['.$locale.']', $post->subcategory->name[$locale] ?? (is_array($post->subcategory->name) ? array_values($post->subcategory->name)[0] : ''));
// Line 59: post title
testEcho('post->title['.$locale.']', $post->title[$locale] ?? (is_array($post->title) ? array_values($post->title)[0] : ''));
// Line 71: eyebrow cat name
testEcho('cat name 2', $post->category->name[$locale] ?? (is_array($post->category->name) ? array_values($post->category->name)[0] : ''));
// Line 75: subcat name 2
testEcho('subcat name 2', $post->subcategory->name[$locale] ?? (is_array($post->subcategory->name) ? array_values($post->subcategory->name)[0] : ''));
// Line 81: post title 2
testEcho('post title 2', $post->title[$locale] ?? (is_array($post->title) ? array_values($post->title)[0] : ''));
// Line 86: excerptText
testEcho('post excerptText()', $post->excerptText());
// Line 95: published_at format
testEcho('post published_at', $post->published_at ? $post->published_at->format('l, F jS, Y') : $post->created_at->format('l, F jS, Y'));
// Line 100: word count
$c1 = strip_tags($post->content[$locale] ?? (is_array($post->content) ? array_values($post->content)[0] : ''));
testEcho('content for word_count', $c1);
testEcho('str_word_count result', str_word_count($c1) . ' words');
// Line 103: min read
testEcho('min read', max(1, (int)ceil(str_word_count($c1) / 220)) . ' min read');
// Line 110: fb sharer
testEcho('route tenant.post', route('tenant.post', ['slug' => $post->slug, 'locale' => $locale]));
// Line 113: x url
testEcho('x text', $post->title[$locale] ?? (is_array($post->title) ? array_values($post->title)[0] : ''));
// Line 127: featured image
testEcho('featured_image', $post->featured_image ?? '');
// Line 128: alt
testEcho('alt title', $post->title[$locale] ?? (is_array($post->title) ? array_values($post->title)[0] : ''));
// Line 138: topAd (raw but check render works)
echo "  · Testing topAd...\n";
try {
    $topAd = \App\Services\AdRendererService::render('above_content');
    echo "    topAd type: " . gettype($topAd) . (is_string($topAd) ? " (".strlen($topAd)." bytes)" : "")."\n";
} catch (\Throwable $e) {
    echo "    ❌ FAIL: ".$e->getMessage()."\n";
}
// Line 149: content (raw, check string)
$contentOut = $post->content[$locale] ?? (is_array($post->content) ? array_values($post->content)[0] : '');
testEcho('post content (raw, should be string)', $contentOut);
// Line 161: tags
foreach ($post->tags as $tag) {
    testEcho('tag name', $tag->name);
}
// Line 196: rp featured_image (if any)
echo "  · Testing related posts e() calls:\n";
foreach ($relatedPosts as $rp) {
    testEcho('  rp slug', $rp->slug);
    testEcho('  rp title', $rp->title[$locale] ?? (is_array($rp->title) ? array_values($rp->title)[0] : ''));
    if ($rp->category) testEcho('  rp cat name', $rp->category->name[$locale] ?? (is_array($rp->category->name) ? array_values($rp->category->name)[0] : ''));
    if ($rp->subcategory) testEcho('  rp subcat name', $rp->subcategory->name[$locale] ?? (is_array($rp->subcategory->name) ? array_values($rp->subcategory->name)[0] : ''));
    testEcho('  rp date', $rp->published_at ? $rp->published_at->format('M d, Y') : $rp->created_at->format('M d, Y'));
}
echo "  · Testing latest posts e() calls:\n";
foreach ($latestPosts as $lp) {
    testEcho('  lp slug', $lp->slug);
    testEcho('  lp title', $lp->title[$locale] ?? (is_array($lp->title) ? array_values($lp->title)[0] : ''));
    testEcho('  lp diffForHumans', $lp->published_at ? $lp->published_at->diffForHumans() : $lp->created_at->diffForHumans());
}

echo "\n=== Now testing tenant-layout variables (css variables, which are likely culprit) ===\n";
echo "(tenant-layout uses \$pageOverrides, \$seo, \$jsonLd, emits CSS vars via ThemeService)\n\n";

// Reconstruct what tenant-layout does
$page = null;
$post_var = $post;  // renamed to avoid shadowing
$bodyClasses = '';
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

$themeSettings = \App\Services\ThemeService::themeSettings();
$darkThemeSettings = \App\Services\ThemeService::darkThemeSettings();

$pageOrPost = $page ?? $post_var ?? null;
echo "pageOrPost: " . (is_object($pageOrPost) ? get_class($pageOrPost) : gettype($pageOrPost)) . "\n";
if ($pageOverrides === null) {
    $pageOverrides = \App\Services\ThemeService::getPageThemeOverrides($pageOrPost);
}
echo "pageOverrides: " . json_encode($pageOverrides) . "\n";

$isDarkMode = false;
$effectiveSettings = \App\Services\ThemeService::getEffectiveThemeSettings($pageOverrides, false);
echo "effectiveSettings count: " . count($effectiveSettings) . "\n";

// THE KEY: test each CSS var in cssVariables output (each is e()'d by Blade when outputting style tags)
$cssVars = \App\Services\ThemeService::cssVariables($effectiveSettings);
echo "cssVariables (dark off) length: " . strlen($cssVars) . "\n";
testEcho('cssVariables string', $cssVars);

// Now split by -- and test each value as if each line was echo'd separately
echo "\nTesting individual theme settings (each value passes through e() in style attribute inline):\n";
foreach ($effectiveSettings as $k => $v) {
    testEcho("theme_setting[$k]", $v);
}

// Also test effective dark settings
$effectiveDarkSettings = \App\Services\ThemeService::getEffectiveThemeSettings($pageOverrides, true);
$cssDarkVars = \App\Services\ThemeService::cssVariables($effectiveDarkSettings);
testEcho('cssDarkVariables string', $cssDarkVars);
echo "\nTesting dark theme settings individual values:\n";
foreach ($effectiveDarkSettings as $k => $v) {
    testEcho("dark_theme_setting[$k]", $v);
}

// Now test SEO fields
echo "\n=== Testing SEO variables ===\n";
echo "seo keys: " . implode(', ', array_keys($seo)) . "\n";
foreach ($seo as $k => $v) {
    testEcho("seo[$k]", $v);
}

// Now test jsonLd
testEcho('jsonLd', $jsonLd);

echo "\n=== Done ===";
