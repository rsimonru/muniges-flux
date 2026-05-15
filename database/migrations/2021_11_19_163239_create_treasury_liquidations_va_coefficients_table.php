<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTreasuryLiquidationsVaCoefficientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treasury_liquidations_va_coefficients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('townhalls_id');
            $table->date('from_date');
            $table->smallInteger('years');
            $table->decimal('coefficient',5,3);
            $table->timestamps();

            $table->foreign('townhalls_id')
                ->references('id')
                ->on('town_halls');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('treasury_liquidations_va_coefficients');
    }
}
