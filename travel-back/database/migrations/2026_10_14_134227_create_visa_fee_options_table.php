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
        Schema::create('visa_fee_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_service_id')->constrained('visa_services')->cascadeOnDelete();
            $table->json('option_name')->nullable();
            $table->json('eligibility_description')->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->json('processing_time')->nullable();
            $table->boolean('interview_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_fee_options');
    }
};
