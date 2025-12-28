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
        Schema::table('tariffs', function (Blueprint $table) {
            $table->string('pax')->change();
            $table->string('reference_id')->nullable()->change();
            $table->string('service_type')->default('transfer')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->integer('pax')->change();
            $table->string('reference_id')->nullable(false)->change();
            $table->string('service_type')->default(null)->change();
        });
    }
};
