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
        Schema::create('form_details', function (Blueprint $table) {
            $table->id();
            $table->integer('business_mode')->default(1); //1-Anually, 2-Semi-annually, 3-Quarterly
            $table->integer('business_type')->default(1); //1-Single, 2-partnership, 3-Corporation,4-Cooperative
            $table->text('business_ammendment'); //object from -to
            $table->boolean('is_enjoying_tax_incentives');
            $table->string('is_enjoying_tax_incentives_no_reason')->nullable();
            $table->boolean('isRented');
            $table->text('lessorDetails')->nullable(); // lessor Name,address,tel. or mobile, name of building, address of building
            $table->string('line_of_business')->nullable(); //e.g. retail, wholesale, service, manufacturing
            $table->string('no_of_units')->nullable(); //e.g. number of stalls, number of employees, number of machines
            $table->string('capitalization')->nullable(); //e.g. initial capital, current capital
            $table->string('gross')->nullable(); // essential, non-essential
            //e.g. gross sales, gross receipts, gross income
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_details');
    }
};
