<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsRegistrationsPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events_registrations_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registrations_id');
            $table->unsignedBigInteger('payments_id');
            $table->string('billing_code',45)->index();
            $table->unsignedBigInteger('bcodes_id')->index();
            $table->timestamps();

            $table->foreign('registrations_id')
                ->references('id')
                ->on('events_registrations');
            $table->foreign('payments_id')
                ->references('id')
                ->on('events_payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events_registrations_payments');
    }
}
