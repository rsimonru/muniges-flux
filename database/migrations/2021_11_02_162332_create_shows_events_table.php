<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowsEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shows_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shows_id');
            $table->unsignedBigInteger('rooms_id');
            $table->json('description');
            $table->string('address',100);
            $table->decimal('price', 6,2);
            $table->smallInteger('capacity');
            $table->smallInteger('protocol');
            $table->smallInteger('ticket_office');
            $table->smallInteger('max_tickets', false, true);
            $table->dateTime('from_date');
            $table->dateTime('to_date');
            $table->dateTime('tickets_from_date');
            $table->dateTime('tickets_to_date');
            $table->decimal('lng', 12, 9)->nullable();
            $table->decimal('lat', 12, 9)->nullable();
            $table->string('image_path',2024)->nullable();
            $table->timestamps();

            $table->foreign('shows_id')
                ->references('id')
                ->on('shows');
            $table->foreign('rooms_id')
                ->references('id')
                ->on('shows_rooms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shows_events');
    }
}
