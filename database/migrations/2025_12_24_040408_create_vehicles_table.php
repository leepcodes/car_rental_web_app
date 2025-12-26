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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained('users')->onDelete('cascade');
            $table->string('license_plate')->unique();
            $table->string('chasis_number')->unique();
            $table->string('brand');
            $table->string('model');
            $table->year('year');       
            $table->string('coding_day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
