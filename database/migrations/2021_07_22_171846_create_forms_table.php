<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('areas_id');
            $table->unsignedBigInteger('townhalls_id');
            $table->string('path',200);
            $table->json('description');
            $table->json('observations')->nullable();
            $table->timestamps();

            $table->foreign('townhalls_id')
                ->references('id')
                ->on('town_halls');
            $table->foreign('areas_id')
                ->references('id')
                ->on('forms_areas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forms');
    }
}
