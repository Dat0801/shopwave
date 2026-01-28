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
        Schema::table('orders', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('status');
            $table->string('shipping_method')->nullable()->after('notes');
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('total_price');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('shipping_cost');
            $table->string('transaction_id')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'notes',
                'shipping_method',
                'shipping_cost',
                'tax_amount',
                'transaction_id',
            ]);
        });
    }
};
