<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreasuryLiquidationsConceptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treasury_liquidations_concepts', function (Blueprint $table) {
            $table->id();
            $table->json('description');
            $table->unsignedBigInteger('ltypes_id');
            $table->unsignedBigInteger('ctypes_id');
            $table->decimal('amount', 12,2);
            $table->decimal('max_amount', 12,2);
            $table->decimal('min_amount', 12,2);
            $table->decimal('interval', 12,2);
            $table->integer('order');
            $table->timestamps();

            $table->foreign('ltypes_id')
                ->references('id')
                ->on('treasury_liquidations_types');
            $table->foreign('ctypes_id')
                ->references('id')
                ->on('treasury_liquidations_concepts_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('treasury_liquidations_concepts');
    }
}
