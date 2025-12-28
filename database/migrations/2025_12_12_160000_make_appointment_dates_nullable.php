<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dateTime('scheduled_at')->nullable()->change();
            $table->dateTime('end_time')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Cannot easily revert to non-nullable without data loss or default values
            // $table->dateTime('scheduled_at')->nullable(false)->change();
        });
    }
};
