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
        Schema::table('provider_services', function (Blueprint $table) {
            if (!Schema::hasColumn('provider_services', 'type')) {
                $table->enum('type', ['transfer', 'tour', 'package'])->default('transfer')->after('provider_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_services', function (Blueprint $table) {
            //
        });
    }
};
