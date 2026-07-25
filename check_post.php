<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$p = \App\Models\Post::first();
echo '--- Post ID: ' . $p->id . PHP_EOL;

$attrs = [
    'title','excerpt','content','meta_title','meta_description',
    'theme_body_bg','theme_body_text','theme_header_bg','theme_footer_bg',
    'theme_primary','theme_accent','theme_section_bg','theme_card_bg',
    'featured_image','slug','category_id','status'
];

foreach ($attrs as $attr) {
    $val = $p->$attr;
    $info = gettype($val);
    if (is_string($val)) {
        $info .= ' len=' . strlen($val);
        if (strlen($val) < 200) {
            $info .= ' val=' . $val;
        }
    } elseif (is_array($val)) {
        $info .= ' keys=' . implode(',', array_keys($val));
    } elseif (is_object($val)) {
        $info .= ' class=' . get_class($val);
    } elseif (is_bool($val)) {
        $info .= ' val=' . ($val ? 'true' : 'false');
    } elseif ($val === null) {
        $info .= ' NULL';
    } else {
        $info .= ' val=' . $val;
    }
    echo $attr . ': ' . $info . PHP_EOL;
}

echo PHP_EOL . '--- Check excerptText() method result' . PHP_EOL;
try {
    echo 'excerptText(): ' . $p->excerptText() . PHP_EOL;
} catch (\Throwable $e) {
    echo 'ERROR in excerptText(): ' . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . '--- Access $p->excerpt (accessor)' . PHP_EOL;
try {
    $excerpt = $p->excerpt;
    echo 'excerpt type: ' . gettype($excerpt) . ' val=' . $excerpt . PHP_EOL;
} catch (\Throwable $e) {
    echo 'ERROR in excerpt accessor: ' . $e->getMessage() . PHP_EOL;
}
