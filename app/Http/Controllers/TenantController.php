<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use App\Services\PageService;
use App\Services\PostService;
use App\Services\SEOService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    protected PostService $postService;
    protected PageService $pageService;
    protected SEOService $seoService;

    public function __construct(
        PostService $postService,
        PageService $pageService,
        SEOService $seoService
    ) {
        $this->postService = $postService;
        $this->pageService = $pageService;
        $this->seoService = $seoService;
    }

    public function home(Request $request, ?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $page = (int) $request->get('page', 1);

        $posts = $this->postService->getPublishedPostsPaginated(12, $page);
        $pages = $this->pageService->getPublishedPages();
        $categories = Category::with('subcategories')->orderBy('id', 'asc')->get();
        $subcategories = Subcategory::active()->ordered()->get(['id', 'name', 'slug', 'category_id']);

        $featuredPosts = $this->postService->getFeaturedPublished(5);
        $latestPosts = $this->postService->getLatestPublished(8);
        $groupedByCategory = $this->postService->getPublishedGroupedByCategory(4, 4);
        $trendingPosts = $this->postService->getLatestPublished(6);

        $seo = $this->seoService->generateTags(null, $locale);
        $jsonLd = $this->seoService->generateJsonLd(null, $locale);

        return view('tenant.home', compact(
            'posts', 'pages', 'categories', 'subcategories',
            'featuredPosts', 'latestPosts', 'groupedByCategory', 'trendingPosts',
            'seo', 'jsonLd', 'locale'
        ));
    }

    public function post(string $slug, ?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        $post = $this->postService->getPostBySlug($slug);
        if (!$post || $post->status !== 'published') {
            abort(404, 'Article not found.');
        }

        $post->loadMissing(['category', 'subcategory', 'tags', 'comments']);

        $pages = $this->pageService->getPublishedPages();
        $categories = Category::with('subcategories')->orderBy('id', 'asc')->get();

        $relatedPosts = $this->postService->getRelatedPosts($post, 6);
        $latestPosts = $this->postService->getLatestPublished(5);
        $featuredPosts = $this->postService->getFeaturedPublished(4);

        $seo = $this->seoService->generateTags($post, $locale);
        $jsonLd = $this->seoService->generateJsonLd($post, $locale);

        return view('tenant.post', compact(
            'post', 'pages', 'categories',
            'relatedPosts', 'latestPosts', 'featuredPosts',
            'seo', 'jsonLd', 'locale'
        ));
    }

    public function page(string $slug, ?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        $page = $this->pageService->getPageBySlug($slug);
        if (!$page || $page->status !== 'published') {
            abort(404, 'Page not found.');
        }

        $pages = $this->pageService->getPublishedPages();
        $latestPosts = $this->postService->getLatestPublished(5);
        $categories = Category::with('subcategories')->orderBy('id', 'asc')->get();

        $seo = $this->seoService->generateTags($page, $locale);
        $jsonLd = $this->seoService->generateJsonLd(null, $locale);

        return view('tenant.page', compact(
            'page', 'pages', 'latestPosts', 'categories',
            'seo', 'jsonLd', 'locale'
        ));
    }

    public function category(Request $request, string $slug, ?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        $category = Category::with('subcategories')->where('slug', $slug)->first();
        if (!$category) {
            abort(404, 'Category not found.');
        }

        $pages = $this->pageService->getPublishedPages();
        $categories = Category::with('subcategories')->orderBy('id', 'asc')->get();
        $pageNum = (int) $request->get('page', 1);
        $posts = $this->postService->getPublishedPostsByCategoryPaginated($category->id, 12, $pageNum);

        $featuredPosts = $this->postService->getFeaturedPublished(5);
        $latestPosts = $this->postService->getLatestPublished(6);

        $seo = $this->seoService->generateTags(null, $locale);
        $siteName = \App\Services\SiteSettings::get('site_name', 'CMS Website');
        $seo['title'] = ($category->name[$locale] ?? reset($category->name)) . ' | ' . $siteName;
        $jsonLd = $this->seoService->generateJsonLd(null, $locale);

        return view('tenant.home', compact(
            'posts', 'pages', 'categories', 'seo', 'jsonLd', 'locale',
            'category', 'featuredPosts', 'latestPosts'
        ));
    }

    public function subcategory(Request $request, string $slug, ?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        $subcategory = Subcategory::with(['category'])->where('slug', $slug)->active()->first();
        if (!$subcategory) {
            abort(404, 'Subcategory not found.');
        }

        $pages = $this->pageService->getPublishedPages();
        $categories = Category::with('subcategories')->orderBy('id', 'asc')->get();
        $pageNum = (int) $request->get('page', 1);
        $posts = $this->postService->getPublishedPostsBySubcategoryPaginated($subcategory->id, 12, $pageNum);

        $featuredPosts = $this->postService->getFeaturedPublished(5);
        $latestPosts = $this->postService->getLatestPublished(6);

        $seo = $this->seoService->generateTags(null, $locale);
        $siteName = \App\Services\SiteSettings::get('site_name', 'CMS Website');
        $seo['title'] = ($subcategory->name[$locale] ?? reset($subcategory->name)) . ' | ' . $siteName;
        $jsonLd = $this->seoService->generateJsonLd(null, $locale);

        return view('tenant.home', compact(
            'posts', 'pages', 'categories', 'seo', 'jsonLd', 'locale',
            'subcategory', 'featuredPosts', 'latestPosts'
        ));
    }
}
