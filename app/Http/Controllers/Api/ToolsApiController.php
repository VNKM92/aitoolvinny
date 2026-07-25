<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ToolsApiController extends Controller
{
    /**
     * POST /api/tools/qr-code
     */
    public function qrCode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'data' => 'required|string|max:1000',
            'size' => 'nullable|integer|min:100|max:500',
        ]);

        $size = $data['size'] ?? 200;
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($data['data']);

        return response()->json([
            'success' => true,
            'qr_code_url' => $qrUrl,
        ]);
    }

    /**
     * POST /api/tools/password
     */
    public function password(Request $request): JsonResponse
    {
        $data = $request->validate([
            'length' => 'nullable|integer|min:8|max:64',
            'numbers' => 'nullable|boolean',
            'symbols' => 'nullable|boolean',
            'uppercase' => 'nullable|boolean',
        ]);

        $length = $data['length'] ?? 16;
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        if ($data['numbers'] ?? true) $chars .= '0123456789';
        if ($data['symbols'] ?? true) $chars .= '!@#$%^&*()_+~`|}{[]:;?><,./-=';
        if ($data['uppercase'] ?? true) $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }

        return response()->json([
            'success' => true,
            'password' => $password,
        ]);
    }

    /**
     * POST /api/tools/uuid
     */
    public function uuid(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'uuid' => Str::uuid()->toString(),
        ]);
    }

    /**
     * POST /api/tools/base64-encode
     */
    public function base64Encode(Request $request): JsonResponse
    {
        $data = $request->validate(['text' => 'required|string']);
        return response()->json([
            'success' => true,
            'encoded' => base64_encode($data['text']),
        ]);
    }

    /**
     * POST /api/tools/base64-decode
     */
    public function base64Decode(Request $request): JsonResponse
    {
        $data = $request->validate(['text' => 'required|string']);
        $decoded = base64_decode($data['text'], true);
        if ($decoded === false) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Base64 format.',
            ], 422);
        }
        return response()->json([
            'success' => true,
            'decoded' => $decoded,
        ]);
    }

    /**
     * POST /api/tools/json-formatter
     */
    public function jsonFormatter(Request $request): JsonResponse
    {
        $data = $request->validate(['text' => 'required|string']);
        $decoded = json_decode($data['text'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid JSON: ' . json_last_error_msg(),
            ], 422);
        }
        return response()->json([
            'success' => true,
            'formatted' => json_encode($decoded, JSON_PRETTY_PRINT),
        ]);
    }

    /**
     * POST /api/tools/sql-formatter
     */
    public function sqlFormatter(Request $request): JsonResponse
    {
        $data = $request->validate(['text' => 'required|string']);
        $sql = $data['text'];
        $keywords = ['select', 'from', 'where', 'and', 'or', 'join', 'on', 'group by', 'order by', 'update', 'delete', 'set'];
        foreach ($keywords as $word) {
            $sql = preg_replace('/\b' . $word . '\b/i', strtoupper($word), $sql);
        }
        return response()->json([
            'success' => true,
            'formatted' => trim($sql),
        ]);
    }

    /**
     * POST /api/tools/html-formatter
     */
    public function htmlFormatter(Request $request): JsonResponse
    {
        $data = $request->validate(['text' => 'required|string']);
        $html = trim($data['text']);
        // Simple server-side line splitter for demo
        $formatted = preg_replace('/(>)(<)/', "$1\n$2", $html);
        return response()->json([
            'success' => true,
            'formatted' => $formatted,
        ]);
    }

    /**
     * POST /api/tools/css-minify
     */
    public function cssMinifier(Request $request): JsonResponse
    {
        $data = $request->validate(['text' => 'required|string']);
        $css = $data['text'];
        $css = preg_replace('/\/\*[\s\S]*?\*\//', '', $css); // strip comments
        $css = preg_replace('/\s*([{\}:;,])\s*/', '$1', $css);
        $css = preg_replace('/\s+/', ' ', $css);
        return response()->json([
            'success' => true,
            'minified' => trim($css),
        ]);
    }

    /**
     * POST /api/tools/js-beautify
     */
    public function jsBeautifier(Request $request): JsonResponse
    {
        $data = $request->validate(['text' => 'required|string']);
        $js = trim($data['text']);
        // Simple tag splitter
        $formatted = preg_replace('/([{}])/', "\n$1\n", $js);
        return response()->json([
            'success' => true,
            'formatted' => trim($formatted),
        ]);
    }

    /**
     * POST /api/tools/word-counter
     */
    public function wordCounter(Request $request): JsonResponse
    {
        $data = $request->validate(['text' => 'required|string']);
        $text = trim($data['text']);
        $words = empty($text) ? 0 : count(preg_split('/\s+/', $text));
        $chars = strlen($data['text']);
        $readingTime = (int) ceil($words / 200);

        return response()->json([
            'success' => true,
            'words' => $words,
            'characters' => $chars,
            'reading_time_minutes' => $readingTime,
        ]);
    }

    /**
     * POST /api/tools/slug-generator
     */
    public function slugGenerator(Request $request): JsonResponse
    {
        $data = $request->validate(['text' => 'required|string']);
        return response()->json([
            'success' => true,
            'slug' => Str::slug($data['text']),
        ]);
    }

    /**
     * POST /api/tools/lorem-ipsum
     */
    public function loremIpsum(Request $request): JsonResponse
    {
        $data = $request->validate(['paragraphs' => 'nullable|integer|min:1|max:10']);
        $paragraphs = $data['paragraphs'] ?? 3;
        $base = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $result = [];
        for ($i = 0; $i < $paragraphs; $i++) {
            $result[] = $base;
        }
        return response()->json([
            'success' => true,
            'lorem' => implode("\n\n", $result),
        ]);
    }

    /**
     * POST /api/tools/age-calculator
     */
    public function ageCalculator(Request $request): JsonResponse
    {
        $data = $request->validate(['birthdate' => 'required|date']);
        $birth = new \DateTime($data['birthdate']);
        $today = new \DateTime();
        $diff = $today->diff($birth);

        return response()->json([
            'success' => true,
            'years' => $diff->y,
            'months' => $diff->m,
            'days' => $diff->d,
            'summary' => "{$diff->y} years, {$diff->m} months, and {$diff->d} days.",
        ]);
    }

    /**
     * POST /api/tools/emi-calculator
     */
    public function emiCalculator(Request $request): JsonResponse
    {
        $data = $request->validate([
            'principal' => 'required|numeric|min:1',
            'rate' => 'required|numeric|min:0.1',
            'tenure' => 'required|integer|min:1',
        ]);

        $p = $data['principal'];
        $r = $data['rate'] / 12 / 100;
        $n = $data['tenure'];

        $emi = ($p * $r * pow(1 + $r, $n)) / (pow(1 + $r, $n) - 1);
        $totalPayable = $emi * $n;
        $interest = $totalPayable - $p;

        return response()->json([
            'success' => true,
            'monthly_emi' => round($emi, 2),
            'total_interest' => round($interest, 2),
            'total_payable' => round($totalPayable, 2),
        ]);
    }

    /**
     * POST /api/tools/gst-calculator
     */
    public function gstCalculator(Request $request): JsonResponse
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'rate' => 'required|numeric|min:0',
        ]);

        $base = $data['amount'];
        $rate = $data['rate'];
        $tax = ($base * $rate) / 100;
        $total = $base + $tax;

        return response()->json([
            'success' => true,
            'base_amount' => round($base, 2),
            'gst_tax' => round($tax, 2),
            'gross_total' => round($total, 2),
        ]);
    }

    /**
     * POST /api/tools/percentage-calculator
     */
    public function percentageCalculator(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|string|in:of,what_percent',
            'value1' => 'required|numeric',
            'value2' => 'required|numeric|min:0.001',
        ]);

        $val1 = $data['value1'];
        $val2 = $data['value2'];
        $result = 0;

        if ($data['type'] === 'of') {
            $result = ($val1 / 100) * $val2;
        } else {
            $result = ($val1 / $val2) * 100;
        }

        return response()->json([
            'success' => true,
            'result' => round($result, 2),
        ]);
    }

    /**
     * POST /api/tools/image-compress
     */
    public function imageCompress(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png|max:4096',
            'quality' => 'nullable|numeric|min:0.1|max:1.0',
        ]);

        $file = $request->file('file');
        $qualityVal = $request->input('quality', 0.75) * 100;

        // Open with GD
        $image = null;
        if ($file->getClientOriginalExtension() === 'png') {
            $image = imagecreatefrompng($file->getRealPath());
        } else {
            $image = imagecreatefromjpeg($file->getRealPath());
        }

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Could not load image resource.',
            ], 422);
        }

        // Compress
        ob_start();
        imagejpeg($image, null, (int)$qualityVal);
        $compressedData = ob_get_clean();
        imagedestroy($image);

        $base64Result = 'data:image/jpeg;base64,' . base64_encode($compressedData);

        return response()->json([
            'success' => true,
            'original_size_kb' => round($file->getSize() / 1024, 2),
            'compressed_size_kb' => round(strlen($compressedData) / 1024, 2),
            'base64_image' => $base64Result,
        ]);
    }
}
