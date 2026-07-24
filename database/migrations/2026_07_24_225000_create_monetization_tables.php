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
        Schema::create('ad_placements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('custom'); // adsense, custom, manager
            $table->string('location'); // header, footer, sidebar, post_top, post_bottom, in_feed, sticky, anchor
            $table->text('code');
            $table->string('destination_url')->nullable(); // For click tracking of custom ads
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('impressions_count')->default(0);
            $table->unsignedInteger('clicks_count')->default(0);
            $table->timestamps();
        });

        Schema::create('affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('keyword')->unique(); // Word to match and replace inside HTML content
            $table->string('target_url');
            $table->unsignedInteger('clicks_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_links');
        Schema::dropIfExists('ad_placements');
    }
};
