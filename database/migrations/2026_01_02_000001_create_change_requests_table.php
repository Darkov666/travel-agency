<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Requester
            $table->string('model_type');
            $table->unsignedBigInteger('model_id')->nullable(); // Nullable for 'create' requests
            $table->string('request_type')->default('update'); // update, create, delete
            $table->json('payload'); // The proposed changes
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('admin_feedback')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_requests');
    }
};
