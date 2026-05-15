<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('events_id');
            $table->string('name', 45);
            $table->string('surname', 75);
            $table->string('vat', 15);
            $table->date('birthday');
            $table->string('address', 75);
            $table->string('town', 75);
            $table->string('province', 45);
            $table->string('zip', 5);
            $table->string('tutor_name', 75)->nullable();
            $table->string('tutor_vat', 15)->nullable();
            $table->string('email', 75);
            $table->string('phone', 15);
            $table->string('iban', 45)->nullable();
            $table->text('observations')->nullable();
            $table->text('internal_note')->nullable();
            $table->unsignedBigInteger('states_id');
            $table->smallInteger('sequential', false, true)->default(0);
            $table->timestamps();

            $table->foreign('events_id')
                ->references('id')
                ->on('events');
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
        Schema::dropIfExists('events_registrations');
    }
}
