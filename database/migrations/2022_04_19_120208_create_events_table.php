<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('townhalls_id');
            $table->json('name');
            $table->dateTime('from_date');
            $table->dateTime('to_date');
            $table->dateTime('inscription_from');
            $table->dateTime('inscription_to');
            $table->json('information')->nullable();
            $table->decimal('price', 6,2);
            $table->tinyInteger('pay_reg_by_activity')->default(1);
            $table->unsignedBigInteger('inscr_templates_id');
            $table->unsignedBigInteger('bonus_templates_id')->index()->nullable();
            $table->unsignedBigInteger('third_templates_id')->index()->nullable();
            $table->unsignedBigInteger('payme_templates_id')->index()->nullable();
            $table->unsignedBigInteger('dorsal_templates_id')->index()->nullable();
            $table->unsignedBigInteger('procedures_id')->index()->nullable();
            $table->unsignedBigInteger('allow_pay');
            $table->tinyInteger('request_iban');
            $table->smallInteger('sequential', false, true)->default(0);
            $table->timestamps();

            $table->foreign('townhalls_id')
                ->references('id')
                ->on('town_halls');
            $table->foreign('inscr_templates_id')
                ->references('id')
                ->on('templates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
