<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCementeryDeathsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cementery_deaths', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('townhalls_id');
            $table->string('register_number', 45)->nullable();
            $table->string('deceased_name', 75);
            $table->string('deceased_vat', 15)->nullable();
            $table->string('deceased_sex', 15)->nullable();
            $table->smallInteger('deceased_age')->nullable();
            $table->string('deceased_address', 75)->nullable();
            $table->string('deceased_town', 75)->nullable();
            $table->string('deceased_province', 45)->nullable();
            $table->string('deceased_zip', 5)->nullable();
            $table->string('death_address', 75)->nullable();
            $table->string('death_town', 75)->nullable();
            $table->string('death_province', 45)->nullable();
            $table->string('death_zip', 5)->nullable();
            $table->dateTime('death_date')->nullable();
            $table->string('wake_town', 75)->nullable();
            $table->string('wake_province', 45)->nullable();
            $table->string('doctor_name', 75)->nullable();
            $table->string('doctor_number', 45)->nullable();
            $table->string('relative_name', 75)->nullable();
            $table->string('relative_vat', 15)->nullable();
            $table->string('relative_address', 75)->nullable();
            $table->string('relative_town', 75)->nullable();
            $table->string('relative_province', 45)->nullable();
            $table->string('relative_zip', 5)->nullable();
            $table->text('death_observations')->nullable();

            $table->dateTime('service_date')->nullable();
            $table->unsignedBigInteger('types_id')->nullable();
            $table->string('funeral_home', 75)->nullable();
            $table->string('cementery_sector', 15)->nullable();
            $table->string('cementery_row', 15)->nullable();
            $table->string('cementery_number', 15)->nullable();
            $table->string('cementery_section', 15)->nullable();
            $table->string('cementery_section_num', 15)->nullable();
            $table->string('cementery_class', 25)->nullable();
            $table->string('cementery_out_destination', 75)->nullable();
            $table->text('cementery_observations')->nullable();

            $table->string('delivery_funeral_home', 75)->nullable();
            $table->dateTime('mortuary_entry_date')->nullable();
            $table->dateTime('mortuary_exit_date')->nullable();
            $table->string('mortuary_treatment', 45)->nullable();
            $table->string('mortuary_treatment_operator', 75)->nullable();
            $table->string('mortuary_treatment_responsable', 75)->nullable();
            $table->string('receives_funeral_home', 75)->nullable();
            $table->string('mortuary_receives_destination', 75)->nullable();
            $table->text('mortuary_observations')->nullable();

            $table->timestamps();

            $table->foreign('townhalls_id')
                ->references('id')
                ->on('town_halls');
            $table->foreign('types_id')
                ->references('id')
                ->on('cementery_services_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cementery_deaths');
    }
}
