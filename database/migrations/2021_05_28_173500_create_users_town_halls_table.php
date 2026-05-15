<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTownHallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_town_halls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('townhalls_id');
            $table->unsignedBigInteger('level_id');
            $table->timestamps();
            $table->softDeletes();
            $table->index('deleted_at', 'users_town_halls_deleted_index');

            $table->foreign('users_id')
                ->references('id')
                ->on('users');
            $table->foreign('townhalls_id')
                ->references('id')
                ->on('town_halls');
            $table->foreign('level_id')
                ->references('id')
                ->on('levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_town_halls');
    }
}
