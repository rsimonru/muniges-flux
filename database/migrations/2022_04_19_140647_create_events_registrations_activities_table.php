<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsRegistrationsActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events_registrations_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registrations_id');
            $table->unsignedBigInteger('activities_id');
            $table->string('group_name', 75)->nullable();
            $table->smallInteger('sequential', false, true)->default(0);
            $table->unsignedBigInteger('states_id');
            $table->dateTime('registration_date');
            $table->dateTime('withdrawl_date')->nullable();
            $table->decimal('registration_discount', 6, 2)->default(0);
            $table->decimal('periodic_discount', 6, 2)->default(0);
            $table->timestamps();

            $table->foreign('registrations_id')
                ->references('id')
                ->on('events_registrations');
            $table->foreign('activities_id')
                ->references('id')
                ->on('events_activities');
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
        Schema::dropIfExists('events_registrations_activities');
    }
}
