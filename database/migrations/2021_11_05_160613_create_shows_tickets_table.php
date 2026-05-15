<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowsTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shows_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sessions_id');
            $table->unsignedBigInteger('states_id');
            $table->smallInteger('sequential', false, true)->default(0);
            $table->dateTime('purchase_date')->nullable();
            $table->dateTime('liberation_date')->nullable();
            $table->string('payment_data', 100)->nullable(true);
            $table->unsignedBigInteger('users_id')->index();
            $table->timestamp('used_at')->nullable();
            $table->string('lock_id', 100)->nullable(true);
            $table->string('name', 75)->nullable();
            $table->string('vat', 15)->nullable();
            $table->string('email', 75)->nullable();
            $table->string('phone', 15)->nullable();
            $table->decimal('amount', 12,2);
            $table->unsignedBigInteger('types_id');
            $table->text('observations')->nullable();
            $table->dateTime('printed_date')->nullable();
            $table->timestamps();

            $table->foreign('sessions_id')
                ->references('id')
                ->on('shows_events_sessions');
            $table->foreign('states_id')
                ->references('id')
                ->on('states');
            $table->foreign('types_id')
                ->references('id')
                ->on('shows_tickets_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shows_tickets');
    }
}
