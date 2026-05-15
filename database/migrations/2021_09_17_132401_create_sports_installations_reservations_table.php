<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSportsInstallationsReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sports_installations_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resources_id');
            $table->dateTime('from_date');
            $table->dateTime('to_date');
            $table->string('name', 45);
            $table->string('vat', 15);
            $table->string('email', 75);
            $table->string('phone', 15);
            $table->unsignedBigInteger('states_id');
            $table->string('billing_code')->index();
            $table->tinyInteger('use_price2');
            $table->dateTime('expiration_date');
            $table->string('origin');
            $table->unsignedBigInteger('origin_reservations_id');
            $table->smallInteger('tickets', false, true);
            $table->smallInteger('tickets2', false, true);
            $table->tinyInteger('rented');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->foreign('resources_id')
                ->references('id')
                ->on('sports_installations_resources');
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
        Schema::dropIfExists('sports_installations_reservations');
    }
}
