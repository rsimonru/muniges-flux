<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreasuryBillingCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treasury_billing_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 45);
            $table->string('vat', 25);
            $table->tinyInteger('passport')->default(0);
            $table->string('thirdparty', 75);
            $table->unsignedBigInteger('procedures_id');
            $table->unsignedBigInteger('townhalls_id');
            $table->string('model', 150)->nullable(true);
            $table->unsignedBigInteger('models_id')->nullable(true);
            $table->string('record', 45)->nullable(true);
            $table->string('liquidation', 45)->nullable(true);
            $table->string('observations', 200)->nullable(true);
            $table->unsignedBigInteger('states_id');
            $table->string('payment_data', 100)->nullable(true);
            $table->decimal('amount', 15, 2);
            $table->dateTime('payment_date')->nullable(true);
            $table->dateTime('expiration_date')->nullable(true);
            $table->string('phase_r', 45)->nullable(true);
            $table->dateTime('r_date')->nullable(true);
            $table->timestamps();

            $table->foreign('procedures_id')
                ->references('id')
                ->on('treasury_procedures');
            $table->foreign('townhalls_id')
                ->references('id')
                ->on('town_halls');
            $table->foreign('states_id')
                ->references('id')
                ->on('states');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('treasury_billing_codes');
    }
}
