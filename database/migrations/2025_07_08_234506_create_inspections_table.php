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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->integer('type'); // 1 - Business, 2 - Stalls
            $table->integer('inspector_id');
            $table->date('scheduled_date');
            $table->text('remarks')->nullable();
            $table->integer('status')->default(0); //0 -Pending, 1-Passed 3-Failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
