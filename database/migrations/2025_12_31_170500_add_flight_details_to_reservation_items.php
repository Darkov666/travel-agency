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
            // Arrival Details
            $table->string('airline')->nullable()->after('passengers_data');
            $table->string('arrival_flight_number')->nullable()->after('airline');
            $table->time('arrival_time')->nullable()->after('arrival_flight_number');
            $table->string('arrival_terminal')->nullable()->after('arrival_time'); // T1, T2, T3, T4

            // Departure Details
            $table->string('departure_airline')->nullable()->after('arrival_terminal');
            $table->string('departure_flight_number')->nullable()->after('departure_airline');
            $table->time('departure_time')->nullable()->after('departure_flight_number');
            $table->string('departure_terminal')->nullable()->after('departure_time');

            // Logic Fields
            $table->enum('flight_type', ['local', 'international'])->default('international')->after('departure_terminal');
            $table->dateTime('pickup_time')->nullable()->after('flight_type'); // Calculated return pickup
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_items', function (Blueprint $table) {
            $table->dropColumn([
                'airline',
                'arrival_flight_number',
                'arrival_time',
                'arrival_terminal',
                'departure_airline',
                'departure_flight_number',
                'departure_time',
                'departure_terminal',
                'flight_type',
                'pickup_time'
            ]);
        });
    }
};
