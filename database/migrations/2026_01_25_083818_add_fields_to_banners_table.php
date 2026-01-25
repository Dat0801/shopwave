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
        Schema::table('banners', function (Blueprint $table) {
            $table->string('mobile_image_path')->nullable()->after('image_path');
            $table->string('button_text')->nullable()->after('description');
            $table->timestamp('start_date')->nullable()->after('button_text');
            $table->timestamp('end_date')->nullable()->after('start_date');
            $table->string('placement')->default('home_hero')->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['button_text', 'start_date', 'end_date', 'placement', 'mobile_image_path']);
        });
    }
};
