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
        Schema::table('services', function (Blueprint $table) {
            // Make duration_minutes nullable if it exists
            if (Schema::hasColumn('services', 'duration_minutes')) {
                $table->integer('duration_minutes')->nullable()->change();
            } else {
                // If it somehow doesn't exist but error says it does (maybe implicit?), add it as nullable
                $table->integer('duration_minutes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Revert is risky if we have nulls, but for strictness:
            // $table->integer('duration_minutes')->nullable(false)->change();
        });
    }
};
