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
        Schema::create('seo_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('source_url')->unique();
            $table->string('target_url');
            $table->integer('status_code')->default(301); // 301 or 302
            $table->timestamps();
        });

        Schema::create('seo_404_logs', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('referrer')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('hits_count')->default(1);
            $table->timestamps();
        });

        Schema::create('seo_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->unique();
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_keywords');
        Schema::dropIfExists('seo_404_logs');
        Schema::dropIfExists('seo_redirects');
    }
};
