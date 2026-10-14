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
        Schema::create('visa_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_country_id')->constrained('visa_countries')->cascadeOnDelete();
            $table->foreignId('visa_type_id')->constrained('visa_types')->cascadeOnDelete();
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->json('processing_time')->nullable();
            $table->json('currency')->nullable();
            $table->decimal('base_fee', 10, 2)->nullable(); // Added scale/precision best practice
            $table->json('eligibility')->nullable();
            $table->string('application_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_services');
    }
};
