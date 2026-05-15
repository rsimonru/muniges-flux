<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('townhalls_id')->nullable()->default(0)->index();
            $table->string('device_token',500)->index();
            $table->string('platform',15)->index();
            $table->string('lang',15)->nullable()->default("es")->index();
            $table->string('version',25);
            $table->tinyInteger('active')->nullable()->default(0);
            $table->dateTime('last_connection')->nullable();
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
        Schema::dropIfExists('devices');
    }
}
