<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_country_id')->constrained('visa_countries')->cascadeOnDelete();
            $table->foreignId('visa_type_id')->constrained('visa_types')->cascadeOnDelete();
            $table->foreignId('visa_document_category_id')->constrained('visa_document_categories')->cascadeOnDelete();
            $table->json('document_name')->nullable();                // one numbered line in the list
            $table->json('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['visa_country_id', 'visa_type_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_documents');
    }
};