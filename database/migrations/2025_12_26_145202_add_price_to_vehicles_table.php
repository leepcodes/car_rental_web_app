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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->double('price')->after('model')->default(0);
            $table->double('rating')->default(5.0)->after('is_active');
            $table->integer('reviews')->default(0)->after('rating');
            $table->text('description')->nullable()->after('price');
            $table->boolean('is_featured')->default(false)->after('description'); // New field for featured vehicles
            $table->json('features')->nullable()->after('is_featured'); // New field for vehicle features
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['price', 'rating', 'reviews', 'description', 'is_featured', 'features']);
        });
    }
};
