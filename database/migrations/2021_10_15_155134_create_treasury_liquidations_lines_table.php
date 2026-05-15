<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreasuryLiquidationsLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treasury_liquidations_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('liquidations_id');
            $table->unsignedBigInteger('concepts_id');
            $table->decimal('quantity', 12,2);
            $table->decimal('amount', 12,2);
            $table->text('text_value')->nullable();
            $table->decimal('value', 12,2)->nullable();
            $table->timestamps();
            $table->unique(['liquidations_id', 'concepts_id']);

            $table->foreign('liquidations_id')
                ->references('id')
                ->on('treasury_liquidations');
            $table->foreign('concepts_id')
                ->references('id')
                ->on('treasury_liquidations_concepts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('treasury_liquidations_lines');
    }
}
