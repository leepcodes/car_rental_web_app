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
        Schema::create('operators', function (Blueprint $table) {
            $table->id();
            $table->string('operators_id');
            $table->string('address');
            $table->integer('age');
            $table->enum('gender',['male','female','others']);
            $table->string('license_number');
            $table->string('license_url');
            $table->string('status');
            $table->boolean('is_profile_completed')->default(0);
            $table->string('verification');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operators');
    }
};
