<?php

namespace App\Livewire\Admin;

use App\Services\AIService;
use App\Services\ActivityLogger;
use Livewire\Component;

class AIGenerator extends Component
{
    public string $activeTab = 'seo_titles';
    public string $topic = '';
    public string $contentInput = '';
    public string $outlineInput = '';
    public string $result = '';
    public bool $isGenerating = false;

    public array $tabs = [
        'seo_titles' => 'SEO Titles',
        'outlines' => 'Outlines',
        'articles' => 'Articles',
        'faqs' => 'FAQs',
        'meta_descriptions' => 'Meta Descriptions',
        'keywords' => 'Keywords',
        'image_prompts' => 'Image Prompts',
        'alt_text' => 'Alt Text',
        'excerpts' => 'Excerpts',
        'youtube_script' => 'YouTube Scripts',
        'shorts_script' => 'Shorts Scripts',
        'facebook_post' => 'Facebook Posts',
        'instagram_caption' => 'Instagram Captions',
        'linkedin_post' => 'LinkedIn Posts',
        'pinterest_description' => 'Pinterest Descriptions',
    ];

    public function selectTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->result = '';
        $this->isGenerating = false;
    }

    public function generate()
    {
        $this->validate([
            'topic' => $this->requiresTopic() ? 'required|string|min:3' : 'nullable',
            'contentInput' => $this->requiresContent() ? 'required|string|min:10' : 'nullable',
        ]);

        $this->isGenerating = true;
        $this->result = '';

        $aiService = app(AIService::class);

        switch ($this->activeTab) {
            case 'seo_titles':
                $this->result = $aiService->generateSEOTitles($this->topic);
                break;
            case 'outlines':
                $this->result = $aiService->generateOutlines($this->topic);
                break;
            case 'articles':
                $this->result = $aiService->generateArticles($this->topic, $this->outlineInput);
                break;
            case 'faqs':
                $this->result = $aiService->generateFaqs($this->topic);
                break;
            case 'meta_descriptions':
                $this->result = $aiService->generateMetaDescriptions($this->contentInput);
                break;
            case 'keywords':
                $this->result = $aiService->generateKeywords($this->contentInput);
                break;
            case 'image_prompts':
                $this->result = $aiService->generateImagePrompts($this->topic);
                break;
            case 'alt_text':
                $this->result = $aiService->generateAltText($this->contentInput);
                break;
            case 'excerpts':
                $this->result = $aiService->generateExcerpts($this->contentInput);
                break;
            case 'youtube_script':
                $this->result = $aiService->generateYouTubeScript($this->topic);
                break;
            case 'shorts_script':
                $this->result = $aiService->generateShortsScript($this->topic);
                break;
            case 'facebook_post':
                $this->result = $aiService->generateFacebookPost($this->topic);
                break;
            case 'instagram_caption':
                $this->result = $aiService->generateInstagramCaption($this->topic);
                break;
            case 'linkedin_post':
                $this->result = $aiService->generateLinkedInPost($this->topic);
                break;
            case 'pinterest_description':
                $this->result = $aiService->generatePinterestDescription($this->topic);
                break;
        }

        $this->isGenerating = false;

        // Log the AI activity (checking if ActivityLogger exists or matches model logging)
        try {
            ActivityLogger::log('ai_generated', "Generated {$this->tabs[$this->activeTab]} content.");
        } catch (\Throwable $e) {
            // Ignore logging issues
        }
    }

    public function requiresTopic(): bool
    {
        return in_array($this->activeTab, [
            'seo_titles', 'outlines', 'articles', 'faqs', 'image_prompts', 
            'youtube_script', 'shorts_script', 'facebook_post', 
            'instagram_caption', 'linkedin_post', 'pinterest_description'
        ]);
    }

    public function requiresContent(): bool
    {
        return in_array($this->activeTab, [
            'meta_descriptions', 'keywords', 'alt_text', 'excerpts'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.ai-generator')
            ->layout('components.layouts.admin');
    }
}
