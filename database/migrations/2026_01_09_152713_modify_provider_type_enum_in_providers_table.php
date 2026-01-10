<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop and Add strategy to resolve persistent modification errors
        try {
            DB::statement("ALTER TABLE providers DROP COLUMN provider_type");
        } catch (\Exception $e) {
            // Ignore if column doesn't exist (idempotency attempt)
        }
        DB::statement("ALTER TABLE providers ADD COLUMN provider_type VARCHAR(50) NOT NULL DEFAULT 'transport'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum (Warning: this might fail if data exists with new types)
        DB::statement("ALTER TABLE providers MODIFY COLUMN provider_type ENUM('transport', 'tour', 'water') DEFAULT 'transport'");
    }
};
