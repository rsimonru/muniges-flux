<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesEventsNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services_events_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('events_id');
            $table->unsignedBigInteger('states_id');
            $table->text('observations');
            $table->string('photo', 200)->nullable();
            $table->unsignedBigInteger('created_user')->index();
            $table->unsignedBigInteger('assigned_user_id')->nullable()->index();
            $table->timestamps();

            $table->foreign('events_id')
                ->references('id')
                ->on('services_events');
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
        Schema::dropIfExists('services_events_notes');
    }
}
