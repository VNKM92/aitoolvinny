<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Http\Request;

$app->instance('request', Request::create('/', 'GET'));

$slug = 'top-10-developer-tips-for-2026-part-1';

try {
    $controller = $app->make(\App\Http\Controllers\TenantController::class);
    $response = $controller->post($slug, null);
    $rendered = $response->render();
    echo "Success! Rendered " . strlen($rendered) . " bytes.\n";
} catch (\Throwable $e) {
    echo "=== EXCEPTION CHAIN (starting from outermost) ===\n";
    $depth = 0;
    $current = $e;
    while ($current) {
        $depth++;
        echo "\n--- Exception #$depth: " . get_class($current) . " ---\n";
        echo "MESSAGE: " . $current->getMessage() . "\n";
        echo "FILE: " . $current->getFile() . " (line " . $current->getLine() . ")\n";

        $trace = $current->getTrace();
        $i = 1;
        echo "TRACE (top 20):\n";
        foreach ($trace as $frame) {
            $line = "#$i ";
            if (isset($frame['file'])) {
                $line .= basename($frame['file']) . ":" . ($frame['line'] ?? '?') . " ";
            }
            if (isset($frame['class'])) {
                $line .= $frame['class'] . ($frame['type'] ?? "::");
            }
            if (isset($frame['function'])) {
                $line .= $frame['function'];
                // Show first 2 args (summarized)
                if (isset($frame['args']) && count($frame['args']) > 0) {
                    $argStrs = [];
                    foreach (array_slice($frame['args'], 0, 3) as $a) {
                        if (is_array($a)) {
                            $argStrs[] = 'Array[' . count($a) . ']';
                        } elseif (is_object($a)) {
                            $argStrs[] = get_class($a);
                        } elseif (is_string($a) && strlen($a) > 40) {
                            $argStrs[] = "'" . substr($a, 0, 40) . "...'";
                        } elseif (is_string($a)) {
                            $argStrs[] = "'$a'";
                        } elseif (is_bool($a)) {
                            $argStrs[] = $a ? 'true' : 'false';
                        } elseif ($a === null) {
                            $argStrs[] = 'null';
                        } else {
                            $argStrs[] = (string)$a;
                        }
                    }
                    $line .= "(" . implode(',', $argStrs) . ")";
                } else {
                    $line .= "()";
                }
            }
            echo "  $line\n";
            if ($i++ >= 20) break;
        }

        $current = method_exists($e, 'getPrevious') ? $e->getPrevious() : null;
        if (!$current && $depth < 3) {
            // Laravel ViewException stores $e in its own way sometimes
            if (property_exists($e, 'previous')) {
                $current = $e->previous;
            }
        }
    }
}
