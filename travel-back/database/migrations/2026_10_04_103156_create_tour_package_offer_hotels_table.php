<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_package_offer_hotels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_package_id')->constrained('tour_packages')->cascadeOnDelete();

            $table->foreignId('tour_package_offer_id')->constrained('tour_package_offers')->cascadeOnDelete();

            $table->json('hotel_name');

            $table->json('location')->nullable();

            $table->unsignedInteger('order')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_package_offer_hotels');
    }
};
