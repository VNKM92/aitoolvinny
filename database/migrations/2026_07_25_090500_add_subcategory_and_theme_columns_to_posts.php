<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'subcategory_id')) {
                $table->foreignId('subcategory_id')
                    ->nullable()
                    ->after('category_id')
                    ->constrained('subcategories')
                    ->nullOnDelete();
            }

            $themeColumns = [
                'theme_body_bg',
                'theme_body_text',
                'theme_header_bg',
                'theme_footer_bg',
                'theme_primary',
                'theme_accent',
                'theme_section_bg',
                'theme_card_bg',
            ];

            foreach ($themeColumns as $col) {
                if (!Schema::hasColumn('posts', $col)) {
                    $table->string($col, 60)->nullable()->after('adsense_enabled');
                }
            }

            if (!Schema::hasColumn('posts', 'excerpt')) {
                $table->json('excerpt')->nullable()->after('content');
            }

            $table->index(['subcategory_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
            $table->dropIndex(['subcategory_id']);

            $cols = [
                'theme_body_bg',
                'theme_body_text',
                'theme_header_bg',
                'theme_footer_bg',
                'theme_primary',
                'theme_accent',
                'theme_section_bg',
                'theme_card_bg',
                'excerpt',
            ];

            foreach ($cols as $col) {
                if (Schema::hasColumn('posts', $col)) {
                    $table->dropColumn($col);
                }
            }

            $table->dropIndex(['posts_subcategory_id_status_index']);
        });
    }
};
