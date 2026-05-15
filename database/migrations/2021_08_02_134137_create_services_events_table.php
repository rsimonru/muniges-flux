<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('townhalls_id');
            $table->unsignedBigInteger('categories_id');
            $table->text('description');
            $table->string('address',200);
            $table->decimal('lon', 8, 5)->default(0);
            $table->decimal('lat', 8, 5)->default(0);
            $table->string('name',200);
            $table->string('phone',200);
            $table->string('email',200);
            $table->unsignedBigInteger('states_id');
            $table->dateTime('closed_at')->nullable();
            $table->unsignedBigInteger('assigned_user_id')->index()->default(0);
            $table->unsignedBigInteger('created_user');
            $table->timestamps();

            $table->foreign('townhalls_id')
                ->references('id')
                ->on('town_halls');
            $table->foreign('categories_id')
                ->references('id')
                ->on('services_categories');
            $table->foreign('created_user')
                ->references('id')
                ->on('users');
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
        Schema::dropIfExists('services_events');
    }
}
