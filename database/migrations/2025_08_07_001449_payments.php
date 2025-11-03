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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('table_rental_id')->nullable();
            $table->integer('stall_rental_id')->nullable();
            $table->integer('volantes_id')->nullable();
            $table->string('receipt')->nullable();
            $table->date('date');
            $table->double('amount');
            $table->string('reference_number')->nullable();
            $table->integer('status')->default(2); // 1 - Paid, //2 - Pending, 3 - Failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
