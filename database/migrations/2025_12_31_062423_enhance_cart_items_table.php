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
        Schema::table('cart_items', function (Blueprint $table) {
            // Drop old link to generic service
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');

            // Add new link to Provider specific service
            $table->foreignId('provider_service_id')->constrained()->cascadeOnDelete()->after('cart_id');

            // Booking Details
            $table->integer('pax')->default(1);
            $table->date('date')->nullable();
            $table->date('return_date')->nullable();
            $table->integer('units')->default(1);

            // Snapshot of price at time of adding
            $table->decimal('price', 10, 2)->default(0);

            // Optional: Store zone to know where it's going (though provider_service has zone_id now)
            // But if provider_services are reusable across zones (unlikely with current design), this helps.
            // Current design: provider_service has strict zone_id. So we don't strictly need it here, but good for quick access.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['provider_service_id']);
            $table->dropColumn([
                'provider_service_id',
                'pax',
                'date',
                'return_date',
                'units',
                'price'
            ]);

            // Restore old column (nullable to avoid issues)
            $table->foreignId('service_id')->nullable()->constrained();
        });
    }
};
