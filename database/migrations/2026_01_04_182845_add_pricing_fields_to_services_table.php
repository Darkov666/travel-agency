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
            $table->decimal('net_price', 10, 2)->nullable()->after('price');
            $table->decimal('commission', 10, 2)->nullable()->after('net_price');
            $table->string('commission_type')->default('fixed')->after('commission'); // fixed, percentage
            $table->string('currency')->default('MXN')->after('commission_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            //
        });
    }
};
