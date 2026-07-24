<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;
use App\Models\PostRevision;
use App\Models\Media;
use App\Models\Comment;
use App\Models\Subscriber;
use App\Models\Faq;
use App\Models\Popup;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\ReusableBlock;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 1. Seed 10 Categories ---
        $categories = [];
        $categoryNames = [
            'Web Development', 'Cloud Computing', 'Artificial Intelligence', 'Cyber Security',
            'Database Administration', 'DevOps & CI/CD', 'Mobile App Development',
            'SaaS Architecture', 'User Experience (UX)', 'Open Source Software'
        ];
        
        foreach ($categoryNames as $index => $name) {
            $slug = Str::slug($name);
            $categories[] = Category::create([
                'name' => [
                    'en' => $name,
                    'es' => $name . ' (ES)'
                ],
                'slug' => $slug,
            ]);
        }
        $this->command->info("Seeded 10 Categories.");

        // --- 2. Seed 10 Tags ---
        $tags = [];
        $tagNames = [
            'laravel', 'php', 'tailwind', 'livewire', 'alpinejs', 
            'mysql', 'docker', 'kubernetes', 'aws', 'vuejs'
        ];

        foreach ($tagNames as $name) {
            $tags[] = Tag::create([
                'name' => [
                    'en' => '#' . $name,
                    'es' => '#' . $name
                ],
                'slug' => $name,
            ]);
        }
        $this->command->info("Seeded 10 Tags.");

        // --- 3. Seed 10 Media Records ---
        $mediaItems = [];
        for ($i = 1; $i <= 10; $i++) {
            $mediaItems[] = Media::create([
                'filename' => "sample_image_{$i}.png",
                'filepath' => "media/sample_image_{$i}.webp",
                'file_type' => 'image/webp',
                'file_size' => 102400 * $i, // dummy size
                'alt_text' => [
                    'en' => "Alt text for sample image {$i}",
                    'es' => "Texto alternativo para la imagen de muestra {$i}"
                ]
            ]);
        }
        $this->command->info("Seeded 10 Media items.");

        // --- 4. Seed 10 Posts ---
        $posts = [];
        for ($i = 1; $i <= 10; $i++) {
            $titleEn = "Top 10 Developer Tips for 2026 - Part {$i}";
            $titleEs = "Los 10 mejores consejos para desarrolladores en 2026 - Parte {$i}";
            $slug = Str::slug($titleEn);

            $post = Post::create([
                'category_id' => $categories[$i - 1]->id,
                'title' => [
                    'en' => $titleEn,
                    'es' => $titleEs
                ],
                'slug' => $slug,
                'content' => [
                    'en' => "<p>This is the full english content body for blog article number {$i}. Writing clean code and adopting solid principles will make your application highly scalable.</p>",
                    'es' => "<p>Este es el cuerpo completo de contenido en español para el artículo del blog número {$i}. Escribir código limpio y adoptar principios sólidos hará que su aplicación sea altamente escalable.</p>"
                ],
                'featured_image' => "posts/sample_image_{$i}.webp",
                'status' => 'published',
                'meta_title' => [
                    'en' => "Dev Tips Part {$i} | SEO Title",
                    'es' => "Consejos Dev Parte {$i} | Título SEO"
                ],
                'meta_description' => [
                    'en' => "Read the latest tech news and tips for developers in article {$i}.",
                    'es' => "Lea las últimas noticias y consejos tecnológicos para desarrolladores en el artículo {$i}."
                ],
                'adsense_enabled' => true,
                'published_at' => now()->subDays(10 - $i),
            ]);

            // Sync random 2 tags
            $randomTags = collect($tags)->random(2)->pluck('id')->toArray();
            $post->tags()->sync($randomTags);

            $posts[] = $post;
        }
        $this->command->info("Seeded 10 Posts & attached Tags.");

        // --- 5. Seed 10 Post Revisions ---
        for ($i = 1; $i <= 10; $i++) {
            $targetPost = $posts[$i - 1];
            PostRevision::create([
                'post_id' => $targetPost->id,
                'user_id' => null, // guest or system
                'title' => $targetPost->title,
                'content' => [
                    'en' => "<p>Auto-saved revision draft version of post {$i} content before final updates.</p>",
                    'es' => "<p>Versión borrador de revisión guardada automáticamente del contenido del post {$i} antes de las actualizaciones finales.</p>"
                ],
                'created_at' => now()->subMinutes(10 * $i),
            ]);
        }
        $this->command->info("Seeded 10 Post Revisions.");

        // --- 6. Seed 10 Comments ---
        for ($i = 1; $i <= 10; $i++) {
            $targetPost = $posts[$i - 1];
            
            // Parent Comment
            $comment = Comment::create([
                'post_id' => $targetPost->id,
                'parent_id' => null,
                'author_name' => "Reader Jack {$i}",
                'author_email' => "jack{$i}@example.com",
                'content' => "This is a very insightful article number {$i}. Thanks for sharing!",
                'status' => 'approved',
            ]);

            if ($i <= 5) {
                // Threaded Reply for first 5 comments
                Comment::create([
                    'post_id' => $targetPost->id,
                    'parent_id' => $comment->id,
                    'author_name' => "Author Editor",
                    'author_email' => "editor@devblog.com",
                    'content' => "Glad you found it helpful, Jack! Let me know if you have any questions.",
                    'status' => 'approved',
                ]);
            }
        }
        $this->command->info("Seeded 10 Comments (including replies).");

        // --- 7. Seed 10 Subscribers ---
        for ($i = 1; $i <= 10; $i++) {
            Subscriber::create([
                'email' => "subscriber_{$i}@domain.com",
                'is_active' => ($i !== 10), // 1 unsubscribed
            ]);
        }
        $this->command->info("Seeded 10 Subscribers.");

        // --- 8. Seed 10 FAQs ---
        $faqs = [
            ['How do I set up Google AdSense?', 'Enter your AdSense Publisher Client ID and slot configurations in the settings dashboard.'],
            ['Is Google AdSense supported?', 'Yes, enter your AdSense Publisher Client ID and slot configurations in the settings dashboard.'],
            ['Can I write articles in Spanish?', 'Yes, the CMS provides localized fields supporting concurrent languages.'],
            ['How does content caching work?', 'Pages and posts queries are cached, and clear hooks trigger updates automatically.'],
            ['Is the template mobile responsive?', 'Yes, the portal wrapper uses liquid grids matching layouts on all mobile sizes.'],
            ['What version of Laravel is active?', 'This platform leverages Laravel 12 features.'],
            ['Which version of PHP is required?', 'PHP 8.2 or greater is required (fully optimized for PHP 8.4).'],
            ['How do I restore deleted posts?', 'Change the view filter to Trash Catalog and click Restore on the post item.'],
            ['Are page uploads optimized?', 'Yes, images are resized and converted to optimized WebP format upon upload.'],
            ['Can I build custom contact forms?', 'Yes, use the Form Builder dashboard to design input forms and view user submissions.']
        ];

        foreach ($faqs as $index => $faq) {
            Faq::create([
                'question' => [
                    'en' => $faq[0],
                    'es' => $faq[0] . ' (ES)'
                ],
                'answer' => [
                    'en' => $faq[1],
                    'es' => $faq[1] . ' (ES)'
                ],
                'order' => $index,
            ]);
        }
        $this->command->info("Seeded 10 FAQs.");

        // --- 9. Seed 10 Popups ---
        for ($i = 1; $i <= 10; $i++) {
            Popup::create([
                'title' => [
                    'en' => "Promotional Offer - Tier {$i}",
                    'es' => "Oferta promocional - Nivel {$i}"
                ],
                'content' => [
                    'en' => "<p>Unlock exclusive dev resources. Subscribe to our newsletter today!</p>",
                    'es' => "<p>Desbloquee recursos exclusivos para desarrolladores. ¡Suscríbase a nuestro boletín hoy!</p>"
                ],
                'is_active' => ($i === 1), // Only 1 active popup
                'starts_at' => now()->subDays(1),
                'ends_at' => now()->addDays(30),
            ]);
        }
        $this->command->info("Seeded 10 Popups.");

        // --- 10. Seed 10 Forms & Submissions ---
        // 1 Primary Form
        $primaryForm = Form::create([
            'name' => 'Contact Support Desk',
            'fields' => [
                ['name' => 'fullname', 'label' => 'Full Name', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'required' => true],
                ['name' => 'message', 'label' => 'Message Body', 'type' => 'textarea', 'required' => true],
            ],
        ]);

        // Seed 9 additional helper forms to satisfy "10 forms"
        for ($f = 1; $f <= 9; $f++) {
            Form::create([
                'name' => "Custom Form Type {$f}",
                'fields' => [
                    ['name' => 'username', 'label' => 'Username', 'type' => 'text', 'required' => true],
                    ['name' => 'rating', 'label' => 'Rating Out of 5', 'type' => 'number', 'required' => false],
                ],
            ]);
        }

        // Seed 10 Submissions for the primary form
        for ($s = 1; $s <= 10; $s++) {
            FormSubmission::create([
                'form_id' => $primaryForm->id,
                'data' => [
                    'fullname' => "User Profile {$s}",
                    'email' => "user{$s}@feedback.com",
                    'message' => "I am submitting response number {$s} to verify form layout rendering.",
                ],
                'created_at' => now()->subHours($s),
            ]);
        }
        $this->command->info("Seeded 10 Forms and 10 Form Submissions.");

        // --- 11. Seed 10 Reusable Blocks ---
        for ($i = 1; $i <= 10; $i++) {
            ReusableBlock::create([
                'name' => "call_to_action_block_{$i}",
                'content' => [
                    'en' => "<h3>Call to Action {$i}</h3><p>Support our open-source projects by subscribing.</p>",
                    'es' => "<h3>Llamado a la acción {$i}</h3><p>Apoye nuestros proyectos de código abierto suscribiéndose.</p>"
                ]
            ]);
        }
        $this->command->info("Seeded 10 Reusable Blocks.");

        // --- 12. Seed 10 Activity Logs ---
        $actions = ['login', 'post_created', 'category_updated', 'settings_changed', 'comment_approved', 'media_uploaded'];
        for ($i = 1; $i <= 10; $i++) {
            ActivityLog::create([
                'user_id' => null, // guest or system
                'action' => $actions[array_rand($actions)],
                'description' => "Seeded activity log number {$i} to audit layout listings.",
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
            ]);
        }
        $this->command->info("Seeded 10 System Activity Logs.");
    }
}
