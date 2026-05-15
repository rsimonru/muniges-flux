<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSportsInstallationsResourcesGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sports_installations_resources_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('installations_id');
            $table->json('name');
            $table->decimal('price', 6,2);
            $table->decimal('price2', 6,2);
            $table->smallInteger('min_slot');
            $table->smallInteger('max_slot');
            $table->smallInteger('slot');
            $table->smallInteger('capacity');
            $table->time('from_hour')->nullable();
            $table->time('to_hour')->nullable();
            $table->smallInteger('max_tickets', false, true);
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
        Schema::dropIfExists('sports_installations_resources_groups');
    }
}
