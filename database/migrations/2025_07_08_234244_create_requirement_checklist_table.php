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
        Schema::create('requirement_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description');
            $table->boolean('isTable');
            $table->boolean('isStall');
            $table->boolean('isVolante');
            $table->boolean('isRequired');
            $table->integer('isTableType'); // 1 - New, 2 - Renew 3 - Both
            $table->integer('isStallType'); // 1 - New, 2 - Renew 3 - Both
            $table->integer('isVolanteType'); // 1 - New, 2 - Renew 3 - Both
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirement_checklists');
    }
};
