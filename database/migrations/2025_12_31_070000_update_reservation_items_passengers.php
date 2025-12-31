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
            $table->dropColumn('minor_ages');
            $table->json('passengers_data')->nullable()->after('pax'); // Stores array of { type: 'adult/minor', age: ?, is_disabled: bool }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_items', function (Blueprint $table) {
            $table->dropColumn('passengers_data');
            $table->json('minor_ages')->nullable();
        });
    }
};
