<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffEmployeesAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_employees_areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employees_id');
            $table->unsignedBigInteger('areas_id');
            $table->timestamps();

            $table->foreign('employees_id')
                ->references('id')
                ->on('staff_employees');
            $table->foreign('areas_id')
                ->references('id')
                ->on('staff_areas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_employees_areas');
    }
}
