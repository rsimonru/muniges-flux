<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('townhalls_id');
            $table->json('description');
            $table->dateTime('from_date');
            $table->dateTime('to_date');
            $table->unsignedBigInteger('templates_id');
            $table->unsignedBigInteger('procedures_id');
            $table->json('information')->nullable();
            $table->timestamps();

            $table->foreign('townhalls_id')
                ->references('id')
                ->on('town_halls');
            $table->foreign('templates_id')
                ->references('id')
                ->on('templates');
            $table->foreign('procedures_id')
                ->references('id')
                ->on('treasury_procedures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shows');
    }
}
