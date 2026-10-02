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
        Schema::create('tour_package_itinerarys', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_package_id')->constrained('tour_packages')->cascadeOnDelete();

            $table->json('title');

            $table->unsignedInteger('day_number');

            $table->json('highlights')->nullable();

            $table->json('overnight')->nullable();

            $table->json('description')->nullable();

            $table->string('map_image')->nullable();

            $table->json('image_caption')->nullable();

            $table->unsignedInteger('order')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique([
                'tour_package_id',
                'day_number'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_package_itinerarys');
    }
};
