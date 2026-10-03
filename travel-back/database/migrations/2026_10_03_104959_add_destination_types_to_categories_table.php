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
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('easy_visa_destination')->default(0)->after('order');
            $table->boolean('popular_destination')->default(0)->after('easy_visa_destination');
            $table->boolean('honeymoon')->default(0)->after('popular_destination');
            $table->boolean('domestic')->default(0)->after('honeymoon');
            $table->boolean('featured')->default(0)->after('domestic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['easy_visa_destination', 'popular_destination', 'honeymoon', 'domestic', 'featured']);
        });
    }
};
