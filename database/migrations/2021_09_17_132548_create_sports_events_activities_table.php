<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSportsEventsActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sports_events_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('events_id');
            $table->json('name');
            $table->smallInteger('quota', false, true);
            $table->smallInteger('registered', false, true)->default(0);
            $table->smallInteger('sequential', false, true)->default(0);
            $table->timestamps();

            $table->foreign('events_id')
                ->references('id')
                ->on('sports_events');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sports_events_activities');
    }
}
