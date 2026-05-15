<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('townhalls_id');
            $table->string('name', 45);
            $table->string('surname', 75);
            $table->string('vat', 15);
            $table->string('ss_number', 15);
            $table->string('address', 75);
            $table->string('town', 75);
            $table->string('province', 45);
            $table->string('zip', 5);
            $table->string('email', 75);
            $table->string('phone', 15);
            $table->string('mobile', 15);
            $table->string('position', 75);
            $table->string('sex', 5);
            $table->date('birthday');
            $table->smallInteger('holidays');
            $table->smallInteger('freedays');
            $table->smallInteger('antiquity');
            $table->decimal('journey_hours',6,3);
            $table->smallInteger('journey_start');
            $table->smallInteger('journey_end');
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
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
        Schema::dropIfExists('staff_employees');
    }
}
