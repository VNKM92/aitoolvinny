<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Http\Request;

$app->instance('request', Request::create('/', 'GET'));
$controller = $app->make(\App\Http\Controllers\TenantController::class);
$locale = null;

$passCount = 0;
$failCount = 0;
function runTest($name, callable $fn) {
    global $passCount, $failCount;
    try {
        $start = microtime(true);
        $result = $fn();
        $bytes = is_string($result) ? strlen($result) : (is_object($result) && method_exists($result, 'render') ? strlen($result->render()) : '?');
        $ms = round((microtime(true) - $start) * 1000, 0);
        echo "  ✓ PASS [$name] $bytes bytes ({$ms}ms)\n";
        $passCount++;
    } catch (\Throwable $e) {
        $failCount++;
        echo "  ❌ FAIL [$name]: " . get_class($e) . ": " . $e->getMessage() . "\n";
        echo "    at " . basename($e->getFile()) . ":" . $e->getLine() . "\n";
        $prev = $e->getPrevious();
        while ($prev) {
            echo "    ↳ " . get_class($prev) . ": " . $prev->getMessage() . " (" . basename($prev->getFile()) . ":" . $prev->getLine() . ")\n";
            $prev = $prev->getPrevious();
        }
    }
}

echo "=== TEST: All 10 Posts ===\n";
$posts = \App\Models\Post::published()->limit(10)->get();
foreach ($posts as $p) {
    runTest("Post #{$p->id}: {$p->slug}", function () use ($controller, $p, $locale) {
        return $controller->post($p->slug, $locale)->render();
    });
}

echo "\n=== TEST: Home Page ===\n";
runTest('home', function () use ($controller, $locale) {
    return $controller->home($locale)->render();
});

echo "\n=== TEST: Pages ===\n";
$pages = \App\Models\Page::published()->limit(5)->get();
foreach ($pages as $pg) {
    runTest("Page: {$pg->slug}", function () use ($controller, $pg, $locale) {
        return $controller->page($pg->slug, $locale)->render();
    });
}

echo "\n=== TEST: Categories ===\n";
$categories = \App\Models\Category::withCount('posts')->having('posts_count', '>', 0)->limit(5)->get();
foreach ($categories as $c) {
    runTest("Category: {$c->slug}", function () use ($controller, $c, $locale) {
        return $controller->category($c->slug, $locale)->render();
    });
}

echo "\n=== TEST: Subcategories ===\n";
$subcategories = \App\Models\Subcategory::active()->limit(5)->get();
foreach ($subcategories as $s) {
    runTest("Subcategory: {$s->slug}", function () use ($controller, $s, $locale) {
        return $controller->subcategory($s->slug, $locale)->render();
    });
}

echo "\n=== TEST: Spanish locale (es) ===\n";
app()->setLocale('es');
runTest('Post #1 (es)', function () use ($controller, $posts) {
    return $controller->post($posts[0]->slug, 'es')->render();
});
runTest('Home (es)', function () use ($controller) {
    return $controller->home('es')->render();
});
app()->setLocale('en');

echo "\n=== TEST: Tools Routes ===\n";
$tools = \App\Models\TrafficTool::active()->limit(3)->get();
$toolsCtrl = $app->make(\App\Http\Controllers\ToolsController::class);
foreach ($tools as $t) {
    runTest("Tool: {$t->slug}", function () use ($toolsCtrl, $t) {
        $resp = $toolsCtrl->show($t->slug);
        if (is_string($resp)) return $resp;
        if (is_object($resp) && method_exists($resp, 'render')) return $resp->render();
        return (string) $resp;
    });
}
if ($tools->count() === 0) {
    runTest('tools index', function () use ($toolsCtrl) {
        $resp = $toolsCtrl->index();
        if (is_string($resp)) return $resp;
        if (is_object($resp) && method_exists($resp, 'render')) return $resp->render();
        return (string) $resp;
    });
}

echo "\n============================================\n";
echo "SUMMARY: $passCount passed, $failCount failed\n";
echo "============================================\n";
exit($failCount > 0 ? 1 : 0);
