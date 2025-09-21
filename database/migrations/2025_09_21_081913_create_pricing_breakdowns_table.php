<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pricing_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();

            $table->decimal('base_amount_in_inr', 14, 2)->default(0.00);
            $table->string('currency', 3)->default('INR');
            $table->text('fx_rate_at_booking')->nullable();
            $table->decimal('total_in_currency', 14, 2)->default(0.00);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_breakdowns');
    }
};
