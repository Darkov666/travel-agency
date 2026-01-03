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
        Schema::table('organizations', function (Blueprint $table) {
            // Legal Information
            $table->string('razon_social')->nullable();
            $table->string('commercial_name')->nullable(); // Can be different from 'name'
            $table->string('rfc')->nullable(); // Tax ID
            $table->string('regimen_fiscal')->nullable();
            $table->string('fiscal_address')->nullable();
            $table->date('company_creation_date')->nullable();

            // Representative Info
            $table->string('representative_name')->nullable();
            $table->string('representative_curp')->nullable();
            $table->string('representative_phone')->nullable();
            $table->string('representative_email')->nullable();

            // Documents (Paths to stored PDFs)
            $table->json('legal_docs')->nullable(); // constancia, ine, comprobante, etc.

            // Domain & Hosting
            $table->enum('hosting_mode', ['subdomain', 'domain'])->default('subdomain');
            $table->string('custom_domain')->nullable()->unique();
            // Slug exists, but usually serves as subdomain

            // Billing & Subscription
            $table->string('stripe_connect_id')->nullable()->unique();
            $table->enum('subscription_status', ['active', 'grace_period', 'suspended', 'cancelled'])->default('active');
            $table->date('last_payment_date')->nullable();
            $table->date('next_payment_date')->nullable();
            $table->decimal('monthly_fee', 10, 2)->default(1000.00);

            // Commission Setting (Root editable)
            $table->decimal('commission_rate', 5, 2)->default(5.00); // 5% default
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn([
                'razon_social',
                'commercial_name',
                'rfc',
                'regimen_fiscal',
                'fiscal_address',
                'company_creation_date',
                'representative_name',
                'representative_curp',
                'representative_phone',
                'representative_email',
                'legal_docs',
                'hosting_mode',
                'custom_domain',
                'stripe_connect_id',
                'subscription_status',
                'last_payment_date',
                'next_payment_date',
                'monthly_fee',
                'commission_rate'
            ]);
        });
    }
};
