<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSportsInstallationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sports_installations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('townhalls_id');
            $table->json('name');
            $table->string('address', 75);
            $table->unsignedBigInteger('templates_id');
            $table->unsignedBigInteger('procedures_id');
            $table->json('information')->nullable();
            $table->string('icon_class', 45);
            $table->string('notify_to')->nullable();
            $table->timestamps();

            $table->foreign('townhalls_id')
                ->references('id')
                ->on('town_halls');
            $table->foreign('templates_id')
                ->references('id')
                ->on('templates');
            $table->foreign('procedures_id')
                ->references('id')
                ->on('treasury_procedures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sports_installations');
    }
}
