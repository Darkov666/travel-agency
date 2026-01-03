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
        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
        });

        Schema::table('zones', function (Blueprint $table) {
            if (!Schema::hasColumn('zones', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
        });

        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
        });

        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_tables', function (Blueprint $table) {
            //
        });
    }
};
