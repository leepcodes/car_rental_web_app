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
            $table->enum('body_type', [
                'Sedan',
                'Hatchback',
                'MPV',
                'SUV',
                'Van',
                'Pickup'
            ])->nullable()->after('year');

            $table->enum('fuel_type', [
                'Gasoline',
                'Diesel',
                'Hybrid',
                'Electric'
            ])->nullable()->after('body_type');

            $table->enum('transmission', [
                'Manual',
                'Automatic',
                'CVT'
            ])->nullable()->after('fuel_type');
   
            $table->string('color')->nullable()->after('transmission');
            $table->integer('seating_capacity')->nullable()->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['body_type', 'fuel_type', 'transmission', 'color', 'seating_capacity']);
        });
    }
};
