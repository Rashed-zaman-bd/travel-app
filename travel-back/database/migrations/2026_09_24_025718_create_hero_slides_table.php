<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();

            // Translatable fields using JSON columns (English + Bengali)
            $table->json('title');
            $table->json('description');
            $table->json('com_name')->nullable();
            $table->json('cta_text')->nullable();
            $table->json('photo_text')->nullable();
            $table->json('location')->nullable();
            $table->json('author_name')->nullable();
            $table->json('photo_date')->nullable();

            // Non-translatable assets & metadata
            $table->string('image');
            $table->string('cta_url')->default('#');
            
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};