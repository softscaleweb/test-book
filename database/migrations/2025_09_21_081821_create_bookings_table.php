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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->enum('type', ['flight', 'hotel']);
            $table->string('item_id')->nullable();

            // relation to users table
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();

            $table->string('currency', 3)->default('INR');
            $table->decimal('total_amount', 14, 2)->default(0.00);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
