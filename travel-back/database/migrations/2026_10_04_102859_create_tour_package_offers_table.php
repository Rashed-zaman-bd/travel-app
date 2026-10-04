<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_package_offers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_package_id')->constrained('tour_packages')->cascadeOnDelete();

            // SUPERIOR / DELUXE
            $table->json('offer_name');

            // Valid From / Valid Till
            $table->date('valid_from')->nullable();
            $table->date('valid_till')->nullable();

            // EVERY DAY
            $table->json('departs')->nullable();

            // BDT 15,000
            $table->decimal('price', 12, 2)->nullable();

            // Price type
            $table->json('price_label')->nullable();

            // Price includes VAT & Tax
            $table->json('price_note')->nullable();

            // Order
            $table->unsignedInteger('order')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_package_offers');
    }
};