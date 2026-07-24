<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\PageService;
use App\Services\PostService;
use App\Services\SEOService;
use App\Services\TenantManager;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    protected TenantManager $tenantManager;
    protected PostService $postService;
    protected PageService $pageService;
    protected SEOService $seoService;

    public function __construct(
        TenantManager $tenantManager, 
        PostService $postService, 
        PageService $pageService,
        SEOService $seoService
    ) {
        $this->tenantManager = $tenantManager;
        $this->postService = $postService;
        $this->pageService = $pageService;
        $this->seoService = $seoService;
    }

    /**
     * Public Tenant Homepage (Blog index list).
     */
    public function home(Request $request, ?string $locale = null)
    {
        $tenant = $this->tenantManager->getTenant();
        $locale = $locale ?: app()->getLocale();

        // 1. Fetch paginated posts (cached)
        $page = (int) $request->get('page', 1);
        $posts = $this->postService->getPublishedPostsPaginated(10, $page);

        // 2. Fetch page list for navigation links (cached)
        $pages = $this->pageService->getPublishedPages();

        // 3. Fetch categories list (cached or simple query scoped by BelongsToTenant)
        $categories = Category::all();

        // 4. Generate SEO Metadata
        $seo = $this->seoService->generateTags(null, $tenant, $locale);
        $jsonLd = $this->seoService->generateJsonLd(null, $tenant, $locale);

        return view('tenant.home', compact('tenant', 'posts', 'pages', 'categories', 'seo', 'jsonLd', 'locale'));
    }

    /**
     * Public Tenant Post Details view.
     */
    public function post(string $slug, ?string $locale = null)
    {
        $tenant = $this->tenantManager->getTenant();
        $locale = $locale ?: app()->getLocale();

        // Fetch post (cached)
        $post = $this->postService->getPostBySlug($slug);

        if (!$post || $post->status !== 'published') {
            abort(404, 'Article not found.');
        }

        $pages = $this->pageService->getPublishedPages();
        $categories = Category::all();

        // Generate SEO & JSON-LD
        $seo = $this->seoService->generateTags($post, $tenant, $locale);
        $jsonLd = $this->seoService->generateJsonLd($post, $tenant, $locale);

        return view('tenant.post', compact('tenant', 'post', 'pages', 'categories', 'seo', 'jsonLd', 'locale'));
    }

    /**
     * Public Tenant Page Details view.
     */
    public function page(string $slug, ?string $locale = null)
    {
        $tenant = $this->tenantManager->getTenant();
        $locale = $locale ?: app()->getLocale();

        // Fetch page (cached)
        $page = $this->pageService->getPageBySlug($slug);

        if (!$page || $page->status !== 'published') {
            abort(404, 'Page not found.');
        }

        $pages = $this->pageService->getPublishedPages();

        // Generate SEO
        $seo = $this->seoService->generateTags($page, $tenant, $locale);
        $jsonLd = $this->seoService->generateJsonLd(null, $tenant, $locale);

        return view('tenant.page', compact('tenant', 'page', 'pages', 'seo', 'jsonLd', 'locale'));
    }

    /**
     * Public Tenant Category filtering view.
     */
    public function category(Request $request, string $slug, ?string $locale = null)
    {
        $tenant = $this->tenantManager->getTenant();
        $locale = $locale ?: app()->getLocale();

        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            abort(404, 'Category not found.');
        }

        $pages = $this->pageService->getPublishedPages();
        $categories = Category::all();

        // Fetch category-specific posts (cached)
        $pageNum = (int) $request->get('page', 1);
        $posts = $this->postService->getPublishedPostsByCategoryPaginated($category->id, 10, $pageNum);

        // Generate SEO metadata
        $seo = $this->seoService->generateTags(null, $tenant, $locale);
        $seo['title'] = ($category->name[$locale] ?? reset($category->name)) . ' | ' . $tenant->name;

        $jsonLd = $this->seoService->generateJsonLd(null, $tenant, $locale);

        return view('tenant.home', compact('tenant', 'posts', 'pages', 'categories', 'seo', 'jsonLd', 'locale', 'category'));
    }
}
