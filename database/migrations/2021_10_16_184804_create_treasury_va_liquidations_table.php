<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreasuryVaLiquidationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treasury_va_liquidations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('townhalls_id');
            $table->unsignedBigInteger('ltypes_id');
            $table->string('acq_name', 75);
            $table->string('acq_address', 75);
            $table->string('acq_town', 75);
            $table->string('acq_province', 45);
            $table->string('acq_zip', 5);
            $table->string('acq_vat', 15);
            $table->string('tra_name', 75);
            $table->string('tra_address', 75);
            $table->string('tra_town', 75);
            $table->string('tra_province', 45);
            $table->string('tra_zip', 5);
            $table->string('tra_vat', 15);
            $table->string('parcel_address', 75);
            $table->string('cadastral_reference', 75);
            $table->unsignedBigInteger('types_id');
            $table->unsignedBigInteger('dtypes_id');
            $table->date('document_date');
            $table->date('transmission_date');
            $table->date('previous_date');
            $table->smallInteger('method')->default(0);
            $table->decimal('terrain_value', 12,2);
            $table->decimal('building_value', 12,2)->default(0);
            $table->decimal('profit', 12,2)->default(0);
            $table->decimal('percent', 5,2);
            $table->string('protocol', 25);
            $table->string('notary', 75);
            $table->decimal('bonus_percent', 5,2);
            $table->decimal('surcharge_percent', 5,2);
            $table->string('billing_code')->index();
            $table->string('email', 75)->nullable();
            $table->string('phone', 15)->nullable();
            $table->text('observations')->nullable();
            $table->json('calculations')->nullable();
            $table->timestamps();

            $table->foreign('townhalls_id')
                ->references('id')
                ->on('town_halls');
            $table->foreign('types_id')
                ->references('id')
                ->on('treasury_va_liquidations_types');
            $table->foreign('dtypes_id')
                ->references('id')
                ->on('treasury_va_liquidations_dtypes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('treasury_va_liquidations');
    }
}
