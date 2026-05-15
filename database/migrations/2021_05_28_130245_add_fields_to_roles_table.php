<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->json('description')->after('guard_name');
            $table->foreignId('level_id')->after('description');
            $table->unsignedBigInteger('townhalls_id')->after('level')->nullable();

            $table->foreign('townhalls_id')
                ->references('id')
                ->on('town_halls');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('description', 'level');
        });
    }
}
