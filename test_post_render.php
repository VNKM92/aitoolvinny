<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Http\Request;

$request = Request::create('/posts/top-10-developer-tips-for-2026-part-1', 'GET');
$app->instance('request', $request);

try {
    $controller = $app->make(\App\Http\Controllers\TenantController::class);
    $response = $controller->post('top-10-developer-tips-for-2026-part-1', 'en');
    echo 'Render succeeded!' . PHP_EOL;
    echo 'Response length: ' . strlen($response->getContent()) . PHP_EOL;
} catch (\Throwable $e) {
    echo 'ERROR TYPE: ' . get_class($e) . PHP_EOL;
    echo 'ERROR MESSAGE: ' . $e->getMessage() . PHP_EOL;
    echo 'ERROR FILE: ' . $e->getFile() . PHP_EOL;
    echo 'ERROR LINE: ' . $e->getLine() . PHP_EOL;
    echo PHP_EOL . '=== FULL TRACE ===' . PHP_EOL;
    $trace = $e->getTrace();
    $i = 1;
    foreach ($trace as $frame) {
        $line = '#' . $i . ' ';
        if (isset($frame['file'])) {
            $line .= basename($frame['file']) . "({$frame['line']}): ";
        } else {
            $line .= '[internal]: ';
        }
        if (isset($frame['class'])) {
            $line .= $frame['class'] . $frame['type'];
        }
        if (isset($frame['function'])) {
            $line .= $frame['function'] . '(';
            if (isset($frame['args'])) {
                $args = [];
                foreach ($frame['args'] as $arg) {
                    if (is_string($arg) && strlen($arg) > 80) {
                        $args[] = "'" . substr($arg, 0, 80) . "...'";
                    } elseif (is_array($arg)) {
                        $args[] = 'Array[' . count($arg) . ']';
                    } elseif (is_object($arg)) {
                        $args[] = get_class($arg);
                    } elseif (is_bool($arg)) {
                        $args[] = $arg ? 'true' : 'false';
                    } elseif ($arg === null) {
                        $args[] = 'null';
                    } else {
                        $args[] = (string)$arg;
                    }
                }
                $line .= implode(', ', $args);
            }
            $line .= ')';
        }
        echo $line . PHP_EOL;
        if ($i++ > 30) break;
    }
}
