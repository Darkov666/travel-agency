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
            $table->string('partner_id')->unique()->nullable()->after('id');
            $table->string('contact_name')->nullable();
            $table->text('full_address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('taxpayer_type', ['physical', 'legal'])->nullable();
            $table->string('logo_path')->nullable();
            $table->string('tax_compliance_path')->nullable();
            $table->enum('provider_type', ['transport', 'tour', 'water'])->default('transport');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn([
                'partner_id',
                'contact_name',
                'full_address',
                'email',
                'phone',
                'taxpayer_type',
                'logo_path',
                'tax_compliance_path',
                'provider_type'
            ]);
        });
    }
};
