<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSportsInstallationsSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sports_installations_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('installations_id');
            $table->dateTime('from_date');
            $table->dateTime('to_date');
            $table->time('from_hour');
            $table->time('to_hour');
            $table->tinyInteger('mon');
            $table->tinyInteger('tue');
            $table->tinyInteger('wed');
            $table->tinyInteger('thu');
            $table->tinyInteger('fri');
            $table->tinyInteger('sat');
            $table->tinyInteger('sun');
            $table->timestamps();

            $table->foreign('installations_id')
                ->references('id')
                ->on('sports_installations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sports_installations_schedules');
    }
}
