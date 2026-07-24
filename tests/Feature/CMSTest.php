<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Page;
use App\Models\User;
use App\Models\Comment;
use App\Services\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CMSTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed site settings
        SiteSettings::set('site_name', 'Tech Blog');
        SiteSettings::set('default_locale', 'en');
        SiteSettings::set('supported_locales', ['en', 'es']);

        // Create standard Admin
        $this->adminUser = User::create([
            'name' => 'CMS Admin',
            'email' => 'admin@cms.com',
            'password' => bcrypt('password'),
            'role' => 'administrator',
        ]);
    }

    /**
     * Test caching and retrieval of global site settings.
     */
    public function test_site_settings_caching_and_helper()
    {
        $this->assertEquals('Tech Blog', SiteSettings::get('site_name'));
        
        // Update setting
        SiteSettings::set('site_name', 'Updated Blog Name');
        $this->assertEquals('Updated Blog Name', SiteSettings::get('site_name'));
    }

    /**
     * Test public frontend home view and locale routing.
     */
    public function test_public_front_homepage_resolves()
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Test with spanish locale segment prefix
        $responseEs = $this->get('/es');
        $responseEs->assertStatus(200);
        $this->assertEquals('es', app()->getLocale());
    }

    /**
     * Test post details rendering.
     */
    public function test_post_details_page_resolves()
    {
        $category = Category::create([
            'name' => ['en' => 'Coding'],
            'slug' => 'coding',
        ]);

        $post = Post::create([
            'category_id' => $category->id,
            'title' => ['en' => 'Welcome Post'],
            'slug' => 'welcome-post',
            'content' => ['en' => 'This is content.'],
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/posts/welcome-post');
        $response->assertStatus(200);
        $response->assertSeeText('Welcome Post');
    }

    /**
     * Test page details rendering.
     */
    public function test_static_page_resolves()
    {
        $page = Page::create([
            'title' => ['en' => 'About Us'],
            'slug' => 'about-us',
            'content' => ['en' => 'About us information.'],
            'status' => 'published',
        ]);

        $response = $this->get('/pages/about-us');
        $response->assertStatus(200);
        $response->assertSeeText('About Us');
    }

    /**
     * Test sitemap XML builder.
     */
    public function test_xml_sitemap_generation()
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
    }

    /**
     * Test comment scoping.
     */
    public function test_comments_approved_scoping()
    {
        $category = Category::create([
            'name' => ['en' => 'Coding'],
            'slug' => 'coding',
        ]);

        $post = Post::create([
            'category_id' => $category->id,
            'title' => ['en' => 'Post'],
            'slug' => 'post-test',
            'content' => ['en' => 'Content'],
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Approved comment
        Comment::create([
            'post_id' => $post->id,
            'author_name' => 'Jack',
            'author_email' => 'jack@mail.com',
            'content' => 'Approved comment',
            'status' => 'approved',
        ]);

        // Pending comment
        Comment::create([
            'post_id' => $post->id,
            'author_name' => 'Spam',
            'author_email' => 'spam@mail.com',
            'content' => 'Spam link',
            'status' => 'pending',
        ]);

        $this->assertEquals(2, Comment::count());
        $this->assertEquals(1, Comment::approved()->count());
    }
}
