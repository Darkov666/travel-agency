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
        Schema::create('reservation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_service_id')->constrained(); // Link to service

            // Snapshot of Service Details
            $table->string('service_name');
            $table->string('provider_name')->nullable();
            $table->string('zone_name')->nullable();

            // Booking Details
            $table->integer('quantity'); // usually 1
            $table->integer('units')->default(1);
            $table->integer('pax');
            $table->json('minor_ages')->nullable(); // Store ages of minors if any

            $table->date('date')->nullable();
            $table->time('time')->nullable(); // If needed later
            $table->date('return_date')->nullable();
            $table->time('return_time')->nullable();

            // Titular Override (if different from main contact)
            $table->string('holder_name')->nullable();

            // Financials per item
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_items');
    }
};
