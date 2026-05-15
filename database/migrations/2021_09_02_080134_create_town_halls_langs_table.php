<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTownHallsLangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('town_halls_langs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('townhalls_id');
            $table->string('lang',15);
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
        Schema::dropIfExists('town_halls_langs');
    }
}
