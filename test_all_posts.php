<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Http\Request;

$app->instance('request', Request::create('/', 'GET'));

$posts = \App\Models\Post::with('category')->limit(10)->get();

foreach ($posts as $p) {
    echo "=== Testing Post ID={$p->id}, slug={$p->slug} ===" . PHP_EOL;

    $request = Request::create("/posts/{$p->slug}", 'GET');
    $app->instance('request', $request);

    try {
        $controller = $app->make(\App\Http\Controllers\TenantController::class);
        $response = $controller->post($p->slug, null);
        // Render the view
        $rendered = $response->render();
        echo "  OK - Rendered " . strlen($rendered) . " bytes" . PHP_EOL;
    } catch (\Throwable $e) {
        echo "  ERROR - " . get_class($e) . ": " . $e->getMessage() . PHP_EOL;
        echo "  FILE: " . $e->getFile() . " LINE: " . $e->getLine() . PHP_EOL;
        // Check attributes
        foreach (['title','excerpt','content','meta_title','meta_description','featured_image','slug'] as $attr) {
            $val = $p->$attr;
            $info = gettype($val);
            if (is_array($val)) {
                $info .= ' [' . implode(',', array_keys($val)) . ']';
                // Check if any value is itself an array (nested!)
                foreach ($val as $k => $v) {
                    if (is_array($v)) {
                        echo "    ** WARNING: $attr.$k is also an ARRAY! (double-nested) **" . PHP_EOL;
                    }
                }
            } elseif (is_string($val)) {
                if (strlen($val) < 100) {
                    $info .= " = " . $val;
                } else {
                    $info .= " (" . strlen($val) . " bytes)";
                }
            } elseif ($val === null) {
                $info .= " = NULL";
            }
            echo "    $attr: $info" . PHP_EOL;
        }
    }
    echo PHP_EOL;
}
