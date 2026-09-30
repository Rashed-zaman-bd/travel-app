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
        Schema::create('tour_package', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
        
            $table->string('hero_image')->nullable();
            $table->json('hero_image_title')->nullable();
            $table->json('hero_image_btn')->nullable();

            $table->json('header')->nullable();
            $table->json('sub_header')->nullable();

            $table->json('package_name');
            $table->string('slug')->unique(); 

            $table->string('package_image');
            $table->json('package_price')->nullable();
            $table->json('package_duration')->nullable();
            $table->json('package_image_title')->nullable();
            $table->string('package_map_image')->nullable();
            $table->json('package_destination')->nullable();

            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_package');
    }
};
