<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSportsEventsPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sports_events_payments', function (Blueprint $table) {
            $table->id();
            $table->json('description');
            $table->unsignedBigInteger('events_id');
            $table->unsignedBigInteger('types_id');
            $table->decimal('amount', 15, 2);
            $table->dateTime('from_date');
            $table->dateTime('to_date');
            $table->timestamps();

            $table->foreign('events_id')
                ->references('id')
                ->on('sports_events');
            $table->foreign('types_id')
                ->references('id')
                ->on('sports_events_payments_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sports_events_payments');
    }
}
