<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('sub_title');
            $table->string('slug')->unique();         
            $table->json('image')->nullable();
            $table->json('image_title')->nullable();
            $table->json('destination_name');
            $table->json('destination_hero_image')->nullable(); 
            $table->json('destination_hero_title')->nullable();
            $table->json('destination_hero_btn')->nullable();
            $table->json('destination_title')->nullable();
            $table->json('destination_sub_title')->nullable();
            $table->string('tour_slug')->nullable()->index();   
            $table->json('tour_title')->nullable();
            $table->json('map_image')->nullable();
            $table->json('tour_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};