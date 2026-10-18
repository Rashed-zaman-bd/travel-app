<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_document_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_service_id')->constrained('visa_services')->cascadeOnDelete();
            $table->json('title');           
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['visa_service_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_document_categories');
    }
};
