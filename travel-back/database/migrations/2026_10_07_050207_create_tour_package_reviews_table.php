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
        Schema::create('tour_package_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_package_id')->constrained('tour_packages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->unsignedTinyInteger('rating'); // 1 to 5 stars
            $table->text('comment')->nullable();
            
            // Traveler Information
            $table->string('traveler_location')->nullable(); 
            $table->string('destination_visited')->nullable(); 
            $table->string('travel_type', 50)->nullable(); 
            $table->date('travel_date')->nullable(); 
            
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            // Optional: Remove this line if users can review the same package on multiple trips
            $table->unique(['tour_package_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_package_reviews');
    }
};