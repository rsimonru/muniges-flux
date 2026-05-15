<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTownHallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('town_halls', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('short_name');
            $table->string('vat',15)->nullable();
            $table->string('address',75)->nullable();
            $table->string('town',75)->nullable();
            $table->string('province',45)->nullable();
            $table->string('zip',5)->nullable();
            $table->string('phone',15)->nullable();
            $table->string('email',75)->nullable();
            $table->string('from_email',75);
            $table->string('web',75);
            $table->string('virtual_office',75)->nullable();
            $table->string('url_prefix',45);
            $table->foreignId('payletter_templates_id')->nullable()->index();
            $table->foreignId('payproof_templates_id')->nullable()->index();
            $table->string('tpv_id',75)->nullable();
            $table->string('tpv_secret',75)->nullable();
            $table->string('tpv_url',150)->nullable();
            $table->string('tpvpc_id',75)->nullable();
            $table->string('tpvpc_secret',75)->nullable();
            $table->string('tpvpc_config_secret',75)->nullable();
            $table->string('app_secret',75)->nullable();
            $table->string('cert_secret',75)->nullable();
            $table->text('lopd_text')->nullable();
            $table->text('payments_text')->nullable();
            $table->decimal('lng', 12, 9)->nullable();
            $table->decimal('lat', 12, 9)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('town_halls');
    }
}
