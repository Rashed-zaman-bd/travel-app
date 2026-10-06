
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
        Schema::create('offer_shows', function (Blueprint $table) {
            $table->id();

            // Localized content
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->json('location')->nullable();
            $table->json('payment_method')->nullable();
            $table->json('discount')->nullable();
            $table->json('tour_duration')->nullable();

            // Price
            $table->decimal('price', 12, 2)->nullable();

            // Slider image
            $table->string('image')->nullable();

            // Button / redirect URL
            $table->string('url')->nullable();

            // Sorting
            $table->unsignedInteger('order')->default(0);

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_shows');
    }
};
