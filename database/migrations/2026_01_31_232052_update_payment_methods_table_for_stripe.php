<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            // Add Stripe payment method ID
            $table->string('stripe_payment_method_id')->nullable()->unique()->after('user_id');

            // Add Stripe card details (from API response)
            $table->string('stripe_card_brand')->nullable()->after('stripe_payment_method_id'); // visa, mastercard, etc
            $table->string('stripe_card_last4')->nullable()->after('stripe_card_brand');
            $table->integer('stripe_card_exp_month')->nullable()->after('stripe_card_last4');
            $table->integer('stripe_card_exp_year')->nullable()->after('stripe_card_exp_month');

            // Keep these for backward compatibility but make nullable with default empty string
            $table->string('type')->nullable()->default('')->change();
            $table->string('holder_name')->nullable()->default('')->change();
            $table->string('last4')->nullable()->default('')->change();
            $table->string('expiry_date')->nullable()->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropUnique(['stripe_payment_method_id']);
            $table->dropColumn([
                'stripe_payment_method_id',
                'stripe_card_brand',
                'stripe_card_last4',
                'stripe_card_exp_month',
                'stripe_card_exp_year',
            ]);
        });
    }
};
