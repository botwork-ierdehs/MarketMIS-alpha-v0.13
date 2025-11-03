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
        Schema::create('stalls_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description');
            $table->boolean('is_transient')->default(false);
            $table->boolean('is_table_rental')->default(false);
            $table->integer('fee_masterlist_id')->nullable();
            $table->timestamps();
        });
    }  

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stalls_categories');
    }
};
