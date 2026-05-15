<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffEmployeesFreedaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_employees_freedays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employees_id');
            $table->dateTime('from_date');
            $table->dateTime('to_date');
            $table->unsignedBigInteger('reasons_id');
            $table->string('observations',500)->nullable();
            $table->timestamps();

            $table->foreign('employees_id')
                ->references('id')
                ->on('staff_employees');
            $table->foreign('reasons_id')
                ->references('id')
                ->on('staff_freedays_reasons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_employees_freedays');
    }
}
