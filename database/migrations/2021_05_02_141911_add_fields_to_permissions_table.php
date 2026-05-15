<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->json('description', 45)->after('name');
            $table->string('class', 45)->after('guard_name')->nullable();
            $table->string('model', 255)->after('class')->nullable();
            $table->foreignId('model_id')->after('model')->nullable();
            $table->smallInteger('level')->after('model_id');
            $table->json('data')->after('level')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('description', 'class', 'model', 'model_id', 'level', 'data');
        });
    }
}
