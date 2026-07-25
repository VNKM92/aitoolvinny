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
        Schema::table('pages', function (Blueprint $table) {
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
                if (!Schema::hasColumn('pages', $col)) {
                    $table->string($col, 60)->nullable()->after('meta_description');
                }
            }

            if (!Schema::hasColumn('pages', 'featured_image')) {
                $table->string('featured_image')->nullable()->after('content');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $cols = [
                'theme_body_bg',
                'theme_body_text',
                'theme_header_bg',
                'theme_footer_bg',
                'theme_primary',
                'theme_accent',
                'theme_section_bg',
                'theme_card_bg',
                'featured_image',
            ];

            foreach ($cols as $col) {
                if (Schema::hasColumn('pages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
