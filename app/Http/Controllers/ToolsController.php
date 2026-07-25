<?php

namespace App\Http\Controllers;

use App\Models\TrafficTool;
use App\Models\Page;
use Illuminate\Http\Request;

class ToolsController extends Controller
{
    /**
     * Show directory list of all active free web tools.
     */
    public function index(Request $request, ?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $pages = Page::where('status', 'published')->orderBy('id', 'asc')->get();

        $search = $request->input('search');

        $query = TrafficTool::where('is_active', true);
        
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tools = $query->orderBy('slug', 'asc')->get();

        $seo = [
            'title' => 'Free Online Web Utility Tools Directory - SEO traffic tools',
            'description' => 'Browse our free online developer converters, encoders, calculations, and content generation traffic tools.',
            'canonical' => url()->current(),
            'robots' => 'index, follow',
        ];

        $jsonLd = '';

        return view('tenant.tools.index', compact('tools', 'pages', 'seo', 'jsonLd', 'locale'));
    }

    /**
     * Show specific free web tool.
     */
    public function show(string $slug, ?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $pages = Page::where('status', 'published')->orderBy('id', 'asc')->get();

        $tool = TrafficTool::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $seo = [
            'title' => $tool->translate('meta_title', $locale),
            'description' => $tool->translate('meta_description', $locale),
            'canonical' => url()->current(),
            'robots' => 'index, follow',
        ];

        $jsonLd = '';

        return view('tenant.tools.show', compact('tool', 'pages', 'seo', 'jsonLd', 'locale'));
    }
}
