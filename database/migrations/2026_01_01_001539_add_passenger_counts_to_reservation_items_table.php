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
            if (!Schema::hasColumn('reservation_items', 'adults')) {
                $table->integer('adults')->default(0);
            }
            if (!Schema::hasColumn('reservation_items', 'children')) {
                $table->integer('children')->default(0);
            }
            if (!Schema::hasColumn('reservation_items', 'infants')) {
                $table->integer('infants')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_items', function (Blueprint $table) {
            if (Schema::hasColumn('reservation_items', 'adults')) {
                $table->dropColumn('adults');
            }
            if (Schema::hasColumn('reservation_items', 'children')) {
                $table->dropColumn('children');
            }
            if (Schema::hasColumn('reservation_items', 'infants')) {
                $table->dropColumn('infants');
            }
        });
    }
};
