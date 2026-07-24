<?php

namespace Tests\Feature;

use App\Services\ImageOptimizer;
use App\Services\SEOHTMLOptimizer;
use App\Services\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test PWA manifest and service worker loading.
     */
    public function test_pwa_endpoints_resolve()
    {
        $responseManifest = $this->get('/manifest.json');
        $responseManifest->assertStatus(200);
        $responseManifest->assertHeader('Content-Type', 'application/json');
        $responseManifest->assertJsonFragment(['display' => 'standalone']);

        $responseSw = $this->get('/sw.js');
        $responseSw->assertStatus(200);
        $responseSw->assertHeader('Content-Type', 'application/javascript');
        $responseSw->assertSeeText('cms-cache-v1');
    }

    /**
     * Test Image CDN path rewriting.
     */
    public function test_image_cdn_rewrites_src()
    {
        SiteSettings::set('image_cdn_url', 'https://cdn.example.com');

        $html = '<img src="/storage/uploads/test.webp">';
        $optimizedHtml = SEOHTMLOptimizer::optimize($html);

        $this->assertStringContainsString('https://cdn.example.com/storage/uploads/test.webp', $optimizedHtml);
    }

    /**
     * Test trusted proxies configuration works (Cloudflare Ready).
     */
    public function test_trusted_proxies_working()
    {
        // Set request headers as if it came through a proxy
        $response = $this->withHeaders([
            'X-Forwarded-For' => '1.2.3.4',
            'X-Forwarded-Proto' => 'https',
        ])->get('/');

        $response->assertStatus(200);
        $this->assertEquals('1.2.3.4', request()->ip());
        $this->assertTrue(request()->secure());
    }

    /**
     * Test ImageOptimizer AVIF and WebP converters.
     */
    public function test_image_optimizer_convert()
    {
        Storage::fake('public');

        // Create a mock transparent PNG image
        $width = 100;
        $height = 100;
        $img = imagecreatetruecolor($width, $height);
        imagesavealpha($img, true);
        $color = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $color);

        $tempFilePath = tempnam(sys_get_temp_dir(), 'test_img') . '.png';
        imagepng($img, $tempFilePath);
        imagedestroy($img);

        $file = new UploadedFile(
            $tempFilePath,
            'test.png',
            'image/png',
            null,
            true
        );

        $optimizer = new ImageOptimizer();
        
        // 1. Convert to WebP
        $webpPath = $optimizer->convertToWebp($file, 'uploads');
        $this->assertStringEndsWith('.webp', $webpPath);
        Storage::disk('public')->assertExists($webpPath);

        // 2. Convert to AVIF (should fall back to webp if AVIF isn't supported in GD environment)
        $avifPath = $optimizer->convertToAvif($file, 'uploads');
        if (function_exists('imageavif')) {
            $this->assertStringEndsWith('.avif', $avifPath);
        } else {
            $this->assertStringEndsWith('.webp', $avifPath);
        }
        Storage::disk('public')->assertExists($avifPath);

        // Clean up
        @unlink($tempFilePath);
    }
}
