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
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('ip_address')->nullable();
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('ip_address')->nullable();
            // Drop the old unique index that required user_id
            $table->dropUnique(['user_id', 'likeable_id', 'likeable_type']);
            // Add new unique index including ip_address for guests, or handle logic in controller
            // For simplicity, we'll just drop the constraint and handle logic in controller, 
            // or add a complex unique constraint. 
            // Let's try to keep it simple: unique on user_id if present, or ip_address if present.
            // But SQL doesn't support conditional unique constraints easily across DBs.
            // So we will just drop the unique constraint and enforce it in the application code.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->dropColumn('ip_address');
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->dropColumn('ip_address');
            $table->unique(['user_id', 'likeable_id', 'likeable_type']);
        });
    }
};
