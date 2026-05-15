<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSportsInstallationsResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sports_installations_resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('groups_id');
            $table->json('name');
            $table->tinyInteger('weight')->default(1);
            $table->tinyInteger('leasable');
            $table->unsignedBigInteger('states_id');
            $table->timestamps();

            $table->foreign('groups_id')
                ->references('id')
                ->on('sports_installations_resources_groups');
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
        Schema::dropIfExists('sports_installations_resources');
    }
}
