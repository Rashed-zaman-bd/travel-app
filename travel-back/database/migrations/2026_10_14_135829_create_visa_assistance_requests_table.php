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
        Schema::create('visa_assistance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_service_id')->nullable()->constrained('visa_services')->nullOnDelete();
            $table->string('service_name')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'contacted', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_assistance_requests');
    }
};
