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
        Schema::create('table_rentals', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('permit_id');
            $table->integer('stall_id');
            $table->string('business_name')->nullable();
            $table->date('started_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('status')->default(0);//0-pending,1- active, 2-expired, 3- cancelled
            $table->boolean('acknowledgeContract')->default(true);
            $table->string('attachment_signature');
            $table->integer('bulb')->default(0);
            $table->double('total_payment');
            $table->string('fees_additional')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_rentals');
    }
};
