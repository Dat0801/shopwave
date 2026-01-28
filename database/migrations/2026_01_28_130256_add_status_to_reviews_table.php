<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('comment');
        });

        // Migrate existing data
        DB::table('reviews')->where('is_approved', true)->update(['status' => 'approved']);
        DB::table('reviews')->where('is_approved', false)->update(['status' => 'pending']);

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('comment');
        });

        // Migrate back
        DB::table('reviews')->where('status', 'approved')->update(['is_approved' => true]);
        DB::table('reviews')->where('status', '!=', 'approved')->update(['is_approved' => false]);

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
