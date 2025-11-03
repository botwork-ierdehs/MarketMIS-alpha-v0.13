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
        Schema::create('permits', function (Blueprint $table) {
            $table->id();
            $table->integer('form_detail_id')->nullable();
            $table->integer('type')->default(1); // 1 - New, 2-Renewal
            $table->integer('status')->default(0);//0-pending,1- approved, 2-rejected, 3- expired 4Close
            $table->text('remarks')->nullable(); // require when status is rejected
            $table->string('permit_number')->nullable();
            $table->string('issued_date')->nullable();
            $table->string('expiry_date')->nullable();
            $table->boolean('is_initial')->default(true); // set to false when forwarded to
            $table->integer('assign_to')->default(2); // 1 user  2 approver 
            $table->integer('department_id')->default(2); 
            // department_id if assign_to === 1 will appear to user as rejected w/remarks, 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permits');
    }
};
