<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreasuryProceduresYearsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treasury_procedures_years', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('procedures_id');
            $table->integer('year');
            $table->string('code',45);
            $table->timestamps();

            $table->foreign('procedures_id')
                ->references('id')
                ->on('treasury_procedures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('treasury_procedures_years');
    }
}
