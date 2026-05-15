<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedules_id');
            $table->string('description',200);
            $table->timestamp('from_date')->nullable();
            $table->timestamp('to_date')->nullable();
            $table->timestamp('notification_date')->nullable();
            $table->tinyInteger('full_day')->nullable()->default(0);
            $table->unsignedBigInteger('created_users_id');
            $table->string('contact',75)->nullable();
            $table->string('address',75)->nullable();
            $table->string('phone',15)->nullable();
            $table->string('observations',500)->nullable();
            $table->timestamps();

            $table->foreign('schedules_id')
                ->references('id')
                ->on('schedules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules_appointments');
    }
}
