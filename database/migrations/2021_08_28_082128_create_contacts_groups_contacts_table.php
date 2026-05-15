<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsGroupsContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts_groups_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contacts_id');
            $table->unsignedBigInteger('groups_id');
            $table->timestamps();

            $table->foreign('contacts_id')
                ->references('id')
                ->on('contacts');
            $table->foreign('groups_id')
                ->references('id')
                ->on('contacts_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts_groups_contacts');
    }
}
