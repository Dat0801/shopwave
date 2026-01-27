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
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('custom'); // custom, route, category, page
            $table->string('url')->nullable(); // For custom URLs or Route names
            $table->unsignedBigInteger('related_id')->nullable(); // For Category ID or Page ID
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->string('location')->default('header'); // header, footer, mobile
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Foreign key for parent_id isn't strictly necessary but good for integrity if we want
            // $table->foreign('parent_id')->references('id')->on('navigation_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
    }
};
