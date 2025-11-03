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
        // OIC PMO, 
        // Office of the Mayor
        // Notary Public
        // MTO
        // PMO
        Schema::create('approval_permits', function (Blueprint $table) {
            $table->id();
            $table->integer('permit_id');
            $table->integer('department_id'); // 1 - BP, 2, Volante, 3 Stall
            $table->integer('approver_id'); // 1 - BP, 2, Volante, 3 Stall
            $table->integer('status')->default(2); // 1 - Paid, //2 - Pending, 3 - Failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_permits');
    }
};
