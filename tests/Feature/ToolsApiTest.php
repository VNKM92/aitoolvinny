<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ToolsApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test simple text transformation APIs.
     */
    public function test_text_utilities_api_endpoints()
    {
        // 1. UUID
        $response = $this->postJson('/api/tools/uuid');
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'uuid']);

        // 2. Base64 Encode
        $response = $this->postJson('/api/tools/base64-encode', ['text' => 'Hello']);
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'encoded' => base64_encode('Hello')
            ]);

        // 3. Base64 Decode
        $response = $this->postJson('/api/tools/base64-decode', ['text' => base64_encode('Hello')]);
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'decoded' => 'Hello'
            ]);

        // 4. Base64 Decode validation fail
        $response = $this->postJson('/api/tools/base64-decode', ['text' => '!!!InvalidBase64!!!']);
        $response->assertStatus(422);
    }

    /**
     * Test code formatting APIs.
     */
    public function test_code_formatters_api_endpoints()
    {
        // JSON Formatter
        $response = $this->postJson('/api/tools/json-formatter', ['text' => '{"a":1}']);
        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true]);

        // JSON Format validation fail
        $response = $this->postJson('/api/tools/json-formatter', ['text' => 'invalid-json']);
        $response->assertStatus(422);

        // SQL Formatter
        $response = $this->postJson('/api/tools/sql-formatter', ['text' => 'select * from table']);
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'formatted' => 'SELECT * FROM table'
            ]);
    }

    /**
     * Test math calculators APIs.
     */
    public function test_math_calculators_api_endpoints()
    {
        // GST Calculator
        $response = $this->postJson('/api/tools/gst-calculator', [
            'amount' => 1000,
            'rate' => 18
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'gst_tax' => 180,
                'gross_total' => 1180
            ]);

        // Percentage of
        $response = $this->postJson('/api/tools/percentage-calculator', [
            'type' => 'of',
            'value1' => 10,
            'value2' => 200
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'result' => 20
            ]);
    }

    /**
     * Test dynamic image upload compression API.
     */
    public function test_image_compress_api_endpoint()
    {
        // Mock GD image upload using UploadedFile fake
        $file = UploadedFile::fake()->image('photo.jpg', 200, 200);

        $response = $this->post('/api/tools/image-compress', [
            'file' => $file,
            'quality' => 0.75
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'original_size_kb', 'compressed_size_kb', 'base64_image']);
    }

    /**
     * Test API Rate Limiting.
     */
    public function test_api_rate_limiting()
    {
        // Clear rate limits
        RateLimiter::clear('api/tools');

        // Simulate 61 attempts -> 61st attempt should be throttled
        for ($i = 0; $i < 60; $i++) {
            $this->postJson('/api/tools/uuid');
        }

        $response = $this->postJson('/api/tools/uuid');
        $response->assertStatus(429);
    }
}
