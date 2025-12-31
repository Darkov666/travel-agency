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
        Schema::table('reservation_items', function (Blueprint $table) {
            $table->enum('vendor_status', ['pending', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('assigned_provider_id')->nullable();
            $table->string('vendor_confirmation_token')->nullable();
            $table->timestamp('vendor_confirmed_at')->nullable();
            $table->decimal('cost', 10, 2)->nullable()->comment('Snapshot of net cost');

            $table->foreign('assigned_provider_id')->references('id')->on('providers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_items', function (Blueprint $table) {
            //
        });
    }
};
