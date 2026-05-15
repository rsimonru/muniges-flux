<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function PHPUnit\Framework\isTrue;

class CreateTreasuryBillingCodesSequentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treasury_billing_codes_sequentials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('townhalls_id');
            $table->unsignedInteger('sequential');
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
        Schema::dropIfExists('treasury_billing_codes_sequentials');
    }
}
