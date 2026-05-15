<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreasuryLiquidationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treasury_liquidations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('townhalls_id');
            $table->unsignedBigInteger('types_id');
            $table->string('name', 45);
            $table->string('address', 75);
            $table->string('town', 75);
            $table->string('province', 45);
            $table->string('zip', 5);
            $table->string('vat', 15);
            $table->string('rep_name', 45)->nullable();
            $table->string('rep_address', 75)->nullable();
            $table->string('rep_town', 75)->nullable();
            $table->string('rep_province', 45)->nullable();
            $table->string('rep_zip', 5)->nullable();
            $table->string('rep_vat', 15)->nullable();
            $table->string('email', 75);
            $table->string('phone', 15);
            $table->string('billing_code')->index();
            $table->text('observations')->nullable();
            $table->string('extra_field_value', 75)->nullable();
            $table->timestamps();

            $table->foreign('townhalls_id')
                ->references('id')
                ->on('town_halls');
            $table->foreign('types_id')
                ->references('id')
                ->on('treasury_liquidations_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('treasury_liquidations');
    }
}
