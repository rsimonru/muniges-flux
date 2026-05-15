<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateStatesModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('states_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('states_id');
            $table->string('model', 100)->index();
            $table->smallInteger('order');
            $table->timestamps();

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
        Schema::dropIfExists('states_models');
    }
}
