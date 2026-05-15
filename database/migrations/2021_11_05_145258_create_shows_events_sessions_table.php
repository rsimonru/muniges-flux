<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowsEventsSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shows_events_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('events_id');
            $table->smallInteger('protocol');
            $table->smallInteger('ticket_office');
            $table->dateTime('date');
            $table->smallInteger('sequential', false, true)->default(0);
            $table->timestamps();

            $table->foreign('events_id')
                ->references('id')
                ->on('shows_events');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shows_events_sessions');
    }
}
