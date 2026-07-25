<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Http\Request;
$app->instance('request', Request::create('/', 'GET'));

$post = \App\Models\Post::with('tags')->find(1);
echo "Post #1 tags (count: " . $post->tags->count() . "):\n";
foreach ($post->tags as $tag) {
    echo "  Tag ID=$tag->id:\n";
    echo "    name type: " . gettype($tag->name) . "\n";
    if (is_array($tag->name)) echo "    name VALUE: " . json_encode($tag->name) . "\n";
    else echo "    name VALUE: " . var_export($tag->name, true) . "\n";
    echo "    tag attributes: " . json_encode($tag->getAttributes()) . "\n\n";
}
