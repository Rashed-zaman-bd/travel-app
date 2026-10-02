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
        Schema::create('tour_package_highlights', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_package_id')->constrained('tour_packages')->cascadeOnDelete();

            $table->json('highlight');

            $table->string('icon')->nullable();

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
        Schema::dropIfExists('tour_package_highlights');
    }
};
