<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events_payments', function (Blueprint $table) {
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
                ->on('events');
            $table->foreign('types_id')
                ->references('id')
                ->on('events_payments_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events_payments');
    }
}
