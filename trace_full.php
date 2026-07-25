<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Http\Request;

$app->instance('request', Request::create('/', 'GET'));

$controller = $app->make(\App\Http\Controllers\TenantController::class);

// Clear view caches first so we have fresh compiled files
$fs = new \Illuminate\Filesystem\Filesystem();
foreach (glob(storage_path('framework/views/*.php')) as $f) {
    @unlink($f);
}
echo "Cleared compiled views, now rendering post...\n";

try {
    $response = $controller->post('top-10-developer-tips-for-2026-part-1', null);
    $html = $response->render();
    echo "SUCCESS (" . strlen($html) . " bytes)\n";
} catch (\Throwable $e) {
    echo "EXCEPTION CAUGHT:\n";
    echo get_class($e) . ": " . $e->getMessage() . "\n\n";

    $depth = 0;
    $cur = $e;
    while ($cur) {
        $depth++;
        echo "--- Layer $depth: " . get_class($cur) . " ---\n";
        echo "    Message: " . $cur->getMessage() . "\n";
        echo "    File: " . $cur->getFile() . " (line " . $cur->getLine() . ")\n\n";
        echo "    Full trace (every frame with file/line):\n";
        $i = 1;
        foreach ($cur->getTrace() as $frame) {
            $line = "    #$i ";
            $file = $frame['file'] ?? 'unknown';
            $frLine = $frame['line'] ?? '?';
            $line .= basename($file) . ":$frLine ";
            if (isset($frame['class'])) $line .= $frame['class'] . ($frame['type'] ?? '::');
            if (isset($frame['function'])) {
                $line .= $frame['function'];
                $args = [];
                if (isset($frame['args'])) {
                    foreach (array_slice($frame['args'], 0, 2) as $a) {
                        if (is_array($a)) $args[] = 'Array['.count($a).']';
                        elseif (is_object($a)) $args[] = get_class($a);
                        elseif (is_string($a) && strlen($a) > 50) $args[] = "'".substr($a,0,50)."...'";
                        elseif (is_string($a)) $args[] = "'$a'";
                        else $args[] = var_export($a, true);
                    }
                }
                $line .= "(".implode(',', $args).")";
            }
            echo $line . "\n";
            $i++;
        }
        echo "\n";
        $cur = $cur->getPrevious();
    }
}

echo "\n\n=== Now list compiled view files (so we can identify which one matches the trace line): ===\n";
foreach (glob(storage_path('framework/views/*.php')) as $f) {
    echo basename($f) . "  " . filesize($f) . " bytes\n";
}
