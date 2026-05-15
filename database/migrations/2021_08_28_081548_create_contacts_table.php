<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('townhalls_id');
            $table->string('name', 45);
            $table->string('surname', 75);
            $table->string('legal_form', 75);
            $table->string('position', 75);
            $table->string('address', 75);
            $table->string('town', 75);
            $table->string('province', 45);
            $table->string('zip', 5);
            $table->string('email', 75);
            $table->string('phone', 15);
            $table->string('mobile', 15);
            $table->string('observations', 500)->nullable();
            $table->unsignedBigInteger('states_id');
            $table->timestamps();

            $table->foreign('townhalls_id')
                ->references('id')
                ->on('town_halls');
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
        Schema::dropIfExists('contacts');
    }
}
