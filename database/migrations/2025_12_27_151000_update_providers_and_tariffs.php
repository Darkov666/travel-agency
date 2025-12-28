<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'is_default')) {
                $table->boolean('is_default')->nullable();
                $table->index('name');
            }
        });

        Schema::table('tariffs', function (Blueprint $table) {
            // Ensure provider_id exists (might already be there, check previous migration if needed)
            // Assuming 2025_12_27_143002_create_tariffs_table.php exists
            if (!Schema::hasColumn('tariffs', 'provider_id')) {
                $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('tariffs', 'zone')) {
                $table->string('zone');
            }
            if (!Schema::hasColumn('tariffs', 'pax')) {
                $table->integer('pax');
            }
            if (!Schema::hasColumn('tariffs', 'cost')) {
                $table->decimal('cost', 10, 2);
            }
            if (!Schema::hasColumn('tariffs', 'price')) {
                $table->decimal('price', 10, 2);
            }
        });
    }

    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn('is_default');
            $table->dropIndex(['name']);
        });

        Schema::table('tariffs', function (Blueprint $table) {
            // Rollback logic (simplified)
            $table->dropColumn(['zone', 'pax', 'cost', 'price']);
        });
    }
};
