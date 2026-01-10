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
        Schema::table('zones', function (Blueprint $table) {
            // Drop the existing unique constraint on name
            // Note: DB driver dependent, but Laravel usually handles index naming as table_column_unique
            $table->dropUnique('zones_name_unique');

            // Add composite unique constraint
            $table->unique(['organization_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            $table->dropUnique(['organization_id', 'name']);
            $table->unique('name');
        });
    }
};
