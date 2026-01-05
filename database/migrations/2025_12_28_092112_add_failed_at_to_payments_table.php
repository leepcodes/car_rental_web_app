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
        Schema::table('payments', function (Blueprint $table) {
            $table->timestamp('failed_at')->nullable()->after('paid_at');
            $table->text('failure_reason')->nullable()->after('failed_at');
            
            // Card payment fields
            $table->string('card_last_four', 4)->nullable()->after('failure_reason');
            $table->string('card_brand')->nullable()->after('card_last_four');
            
            // E-wallet payment fields
            $table->string('ewallet_number')->nullable()->after('card_brand');
            $table->string('ewallet_email')->nullable()->after('ewallet_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'failed_at',
                'failure_reason',
                'card_last_four',
                'card_brand',
                'ewallet_number',
                'ewallet_email'
            ]);
        });
    }
};
