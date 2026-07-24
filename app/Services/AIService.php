<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Call the Gemini API model to generate content.
     */
    public function callGemini(string $prompt): string
    {
        $apiKey = SiteSettings::get('gemini_api_key') ?: env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            return "Error: Gemini API Key is not configured. Please add it to your Settings or .env file.";
        }

        // Use gemini-2.5-flash as the default model (or gemini-1.5-flash)
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 2048,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Error: Empty content returned from Gemini.';
            }

            Log::error('Gemini API Error: ' . $response->body());
            return "Error: Gemini API returned status code " . $response->status() . ". Details: " . $response->json('error.message', 'Unknown API Error');

        } catch (\Throwable $e) {
            Log::error('Gemini Connection Exception: ' . $e->getMessage());
            return "Error: Failed to connect to Gemini API. Message: " . $e->getMessage();
        }
    }

    public function generateSEOTitles(string $topic): string
    {
        $prompt = "Generate 5 high-converting, click-worthy, and SEO-optimized title ideas for the topic: \"{$topic}\". Format as a clean markdown list. Do not write introductory or concluding text.";
        return $this->callGemini($prompt);
    }

    public function generateOutlines(string $topic): string
    {
        $prompt = "Create a comprehensive, well-structured article outline for the topic: \"{$topic}\". Include main headings (H2) and subheadings (H3). Format as clean markdown.";
        return $this->callGemini($prompt);
    }

    public function generateArticles(string $topic, ?string $outline = null): string
    {
        $prompt = "Write a comprehensive, professional, and engaging article on the topic: \"{$topic}\".";
        if (!empty($outline)) {
            $prompt .= " Follow this outline: {$outline}.";
        }
        $prompt .= " Use markdown formatting with clear headings, paragraphs, and lists. Make it in-depth, structured, and informative.";
        return $this->callGemini($prompt);
    }

    public function generateFaqs(string $topic): string
    {
        $prompt = "Generate 5 frequently asked questions (FAQs) with detailed, helpful answers on the topic: \"{$topic}\". Format as bold Questions followed by Answers.";
        return $this->callGemini($prompt);
    }

    public function generateMetaDescriptions(string $content): string
    {
        $prompt = "Write an engaging, SEO-optimized meta description (150-160 characters) summarizing the following content: \"{$content}\". Focus on click-through rate optimization. Provide only the description text.";
        return $this->callGemini($prompt);
    }

    public function generateKeywords(string $content): string
    {
        $prompt = "Extract the top 10 relevant SEO keywords and tags for the following content: \"{$content}\". Format as a comma-separated list. Do not write introductory text.";
        return $this->callGemini($prompt);
    }

    public function generateImagePrompts(string $topic): string
    {
        $prompt = "Create 3 detailed, descriptive image prompts for Midjourney, DALL-E, or Stable Diffusion to illustrate the topic: \"{$topic}\". Provide specific styling (e.g., photo-realistic, digital art).";
        return $this->callGemini($prompt);
    }

    public function generateAltText(string $imageContext): string
    {
        $prompt = "Write a descriptive, concise image Alt Text (10-15 words) for an image with the following description/context: \"{$imageContext}\". Provide only the Alt Text.";
        return $this->callGemini($prompt);
    }

    public function generateExcerpts(string $content): string
    {
        $prompt = "Write a short, engaging article excerpt or summary (2-3 sentences, max 80 words) for the following content: \"{$content}\". Provide only the excerpt text.";
        return $this->callGemini($prompt);
    }

    public function generateYouTubeScript(string $topic): string
    {
        $prompt = "Write a complete, engaging YouTube video script on the topic: \"{$topic}\". Include host lines, visual descriptions/cues in brackets, intro, body points, and a strong call-to-action (CTA) to subscribe.";
        return $this->callGemini($prompt);
    }

    public function generateShortsScript(string $topic): string
    {
        $prompt = "Write a fast-paced, high-retention 60-second YouTube Shorts/TikTok script on the topic: \"{$topic}\". Format with visual cues and quick, punchy speech lines.";
        return $this->callGemini($prompt);
    }

    public function generateFacebookPost(string $topic): string
    {
        $prompt = "Create an engaging Facebook post about: \"{$topic}\". Include relevant emojis, a hook, key points, and a clear call-to-action or question to drive comments.";
        return $this->callGemini($prompt);
    }

    public function generateInstagramCaption(string $topic): string
    {
        $prompt = "Create a visually engaging Instagram caption about: \"{$topic}\". Use a strong hook, emojis, bulleted list if necessary, and a group of 5-8 relevant hashtags.";
        return $this->callGemini($prompt);
    }

    public function generateLinkedInPost(string $topic): string
    {
        $prompt = "Write a professional, high-engagement LinkedIn post about: \"{$topic}\". Structure it with a compelling hook, short paragraphs, bullet points, key takeaways, and relevant hashtags.";
        return $this->callGemini($prompt);
    }

    public function generatePinterestDescription(string $topic): string
    {
        $prompt = "Create a keyword-rich, engaging Pinterest pin description (max 500 characters) about: \"{$topic}\". Include relevant hashtags. Provide only the description text.";
        return $this->callGemini($prompt);
    }
}
