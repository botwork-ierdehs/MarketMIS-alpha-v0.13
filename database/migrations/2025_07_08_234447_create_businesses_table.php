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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('permit_id');
            $table->string('name');
            $table->string('trade_or_franchise_name')->nullable();
            $table->string('business_address');
            $table->string('business_phone')->nullable();
            $table->string('business_email');
            $table->string('business_telephone');
            $table->string('area_of_sqr_meter')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
