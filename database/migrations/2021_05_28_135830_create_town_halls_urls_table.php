<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTownHallsUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('town_halls_urls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('townhalls_id');
            $table->string('type',15);
            $table->string('url',100);
            $table->integer('order');
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
        Schema::dropIfExists('town_halls_urls');
    }
}
