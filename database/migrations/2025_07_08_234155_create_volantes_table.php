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
        Schema::create('volantes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('permit_id');
            $table->integer('stall_id');
            $table->string('business_name')->nullable();
            $table->integer('status')->default(2);//1- Occupied, 2-Vacant, 3- Reserved'
            $table->date('started_date')->nullable();;
            $table->date('end_date')->nullable();;
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
        Schema::dropIfExists('volantes');
    }
};
