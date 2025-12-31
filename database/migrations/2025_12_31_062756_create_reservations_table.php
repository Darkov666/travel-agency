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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid('booking_ref')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Contact Details
            $table->string('contact_name');
            $table->string('contact_surname')->nullable();
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->string('contact_nationality')->nullable(); // Optional?

            // Payment Details
            $table->decimal('total_amount', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance_due', 10, 2)->default(0);

            $table->enum('payment_method', ['paypal', 'transfer', 'cash', 'stripe'])->default('transfer');
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->enum('status', ['draft', 'pending', 'confirmed', 'completed', 'cancelled'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
