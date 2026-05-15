<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('description',45);
            $table->json('content');
            $table->unsignedBigInteger('tobjects_id');
            $table->unsignedBigInteger('tsections_id');
            $table->unsignedBigInteger('ttypes_id');
            $table->unsignedBigInteger('townhalls_id')->index();
            $table->tinyInteger('active')->default(1);
            $table->timestamps();

            $table->foreign('tobjects_id')
            ->references('id')
            ->on('templates_objects');
            $table->foreign('tsections_id')
            ->references('id')
            ->on('templates_sections');
            $table->foreign('ttypes_id')
            ->references('id')
            ->on('templates_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('templates');
    }
}
