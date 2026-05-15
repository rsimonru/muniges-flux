<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSportsInstallationsHolidaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sports_installations_holidays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('installations_id');
            $table->date('date');
            $table->timestamps();

            $table->foreign('installations_id')
                ->references('id')
                ->on('sports_installations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sports_installations_holidays');
    }
}
