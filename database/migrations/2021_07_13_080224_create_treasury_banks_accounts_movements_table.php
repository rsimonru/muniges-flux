<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreasuryBanksAccountsMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treasury_banks_accounts_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accounts_id');
            $table->dateTime('accounting_date');
            $table->dateTime('value_date');
            $table->string('concept', 512);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance', 15, 2);
            $table->string('thirdparty', 75)->nullable(true);
            $table->string('vat' ,15)->nullable(true);
            $table->string('record', 45)->nullable(true);
            $table->string('internal_concept', 512)->nullable(true);
            $table->string('more_info', 512)->nullable(true);
            $table->unsignedBigInteger('bcodes_id')->index()->default(0);
            $table->string('phase_r', 45)->nullable(true);
            $table->dateTime('r_date')->nullable(true);
            $table->timestamps();

            $table->foreign('accounts_id')
                ->references('id')
                ->on('treasury_banks_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('treasury_banks_accounts_movements');
    }
}
