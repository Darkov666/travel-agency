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
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();

            // Link to the specific Reservation Item (Leg)
            $table->foreignId('reservation_item_id')->constrained()->cascadeOnDelete();

            // Driver Assignment
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();

            // Vehicle Assignment (Optional but recommended)
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();

            // Inherited Folio for Searchability
            $table->string('folio')->index(); // Copied from Reservation->booking_ref

            // Status Workflow
            $table->enum('status', [
                'pending',          // Created, not assigned
                'assigned',         // Driver Assigned, waiting acceptance
                'accepted',         // Driver Accepted
                'rejected',         // Driver Rejected (loops back to pending/assigned)
                'en_route_base',    // Checkpoint 1: Leaving Base
                'at_pickup',        // Checkpoint 2: Arrived at Hotel/Airport
                'on_board',         // Checkpoint 3: Pax on board (Start Trip)
                'finished',         // Checkpoint 4: Drop-off complete
                'cancelled'         // Service cancelled
            ])->default('pending');

            // Tracking Data
            $table->decimal('current_lat', 10, 8)->nullable();
            $table->decimal('current_lng', 11, 8)->nullable();

            // Timestamps Log (JSON) for exact checkpoint times
            $table->json('checkpoints')->nullable();

            // Driver Final Comments
            $table->text('comments')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
