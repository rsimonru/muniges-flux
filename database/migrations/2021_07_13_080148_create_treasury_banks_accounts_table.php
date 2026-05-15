<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreasuryBanksAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treasury_banks_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('banks_id');
            $table->unsignedBigInteger('townhalls_id');
            $table->string('iban',45);
            $table->string('alias',45);
            $table->string('ordinal',45);
            $table->tinyInteger('public');
            $table->string('n43_name',45);
            $table->timestamps();

            $table->foreign('banks_id')
                ->references('id')
                ->on('treasury_banks');
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
        Schema::dropIfExists('treasury_banks_accounts');
    }
}
