<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateProvincesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('province',75)->nullable();
            $table->integer('code');
            $table->timestamps();
        });
        DB::table('provinces')->insert(array('province' =>'Álava','code' =>'1'));
        DB::table('provinces')->insert(array('province' =>'Albacete','code' =>'2'));
        DB::table('provinces')->insert(array('province' =>'Alicante','code' =>'3'));
        DB::table('provinces')->insert(array('province' =>'Almería','code' =>'4'));
        DB::table('provinces')->insert(array('province' =>'Ávila','code' =>'5'));
        DB::table('provinces')->insert(array('province' =>'Badajoz','code' =>'6'));
        DB::table('provinces')->insert(array('province' =>'Baleares','code' =>'7'));
        DB::table('provinces')->insert(array('province' =>'Barcelona','code' =>'8'));
        DB::table('provinces')->insert(array('province' =>'Burgos','code' =>'9'));
        DB::table('provinces')->insert(array('province' =>'Cáceres','code' =>'10'));
        DB::table('provinces')->insert(array('province' =>'Cádiz','code' =>'11'));
        DB::table('provinces')->insert(array('province' =>'Castellón','code' =>'12'));
        DB::table('provinces')->insert(array('province' =>'Ciudad Real','code' =>'13'));
        DB::table('provinces')->insert(array('province' =>'Córdoba','code' =>'15'));
        DB::table('provinces')->insert(array('province' =>'A Coruña','code' =>'15'));
        DB::table('provinces')->insert(array('province' =>'Cuenca','code' =>'16'));
        DB::table('provinces')->insert(array('province' =>'Girona','code' =>'17'));
        DB::table('provinces')->insert(array('province' =>'Granada','code' =>'18'));
        DB::table('provinces')->insert(array('province' =>'Guadalajara','code' =>'19'));
        DB::table('provinces')->insert(array('province' =>'Gipuzkoa','code' =>'20'));
        DB::table('provinces')->insert(array('province' =>'Huelva','code' =>'21'));
        DB::table('provinces')->insert(array('province' =>'Huesca','code' =>'22'));
        DB::table('provinces')->insert(array('province' =>'Jaén','code' =>'23'));
        DB::table('provinces')->insert(array('province' =>'León','code' =>'24'));
        DB::table('provinces')->insert(array('province' =>'Lérida','code' =>'25'));
        DB::table('provinces')->insert(array('province' =>'La Rioja','code' =>'26'));
        DB::table('provinces')->insert(array('province' =>'Lugo','code' =>'27'));
        DB::table('provinces')->insert(array('province' =>'Madrid','code' =>'28'));
        DB::table('provinces')->insert(array('province' =>'Málaga','code' =>'29'));
        DB::table('provinces')->insert(array('province' =>'Murcia','code' =>'30'));
        DB::table('provinces')->insert(array('province' =>'Navarra','code' =>'31'));
        DB::table('provinces')->insert(array('province' =>'Ourense','code' =>'32'));
        DB::table('provinces')->insert(array('province' =>'Asturias','code' =>'33'));
        DB::table('provinces')->insert(array('province' =>'Palencia','code' =>'36'));
        DB::table('provinces')->insert(array('province' =>'Las Palmas','code' =>'35'));
        DB::table('provinces')->insert(array('province' =>'Pontevedra','code' =>'36'));
        DB::table('provinces')->insert(array('province' =>'Salamanca','code' =>'37'));
        DB::table('provinces')->insert(array('province' =>'Sta. Cruz Tenerife','code' =>'38'));
        DB::table('provinces')->insert(array('province' =>'Cantabria','code' =>'39'));
        DB::table('provinces')->insert(array('province' =>'Segovia','code' =>'40'));
        DB::table('provinces')->insert(array('province' =>'Sevilla','code' =>'41'));
        DB::table('provinces')->insert(array('province' =>'Soria','code' =>'42'));
        DB::table('provinces')->insert(array('province' =>'Tarragona','code' =>'43'));
        DB::table('provinces')->insert(array('province' =>'Teruel','code' =>'44'));
        DB::table('provinces')->insert(array('province' =>'Toledo','code' =>'45'));
        DB::table('provinces')->insert(array('province' =>'Valencia','code' =>'46'));
        DB::table('provinces')->insert(array('province' =>'Valladolid','code' =>'47'));
        DB::table('provinces')->insert(array('province' =>'Vizcaya','code' =>'48'));
        DB::table('provinces')->insert(array('province' =>'Zamora','code' =>'49'));
        DB::table('provinces')->insert(array('province' =>'Zaragoza','code' =>'50'));
        DB::table('provinces')->insert(array('province' =>'Ceuta','code' =>'51'));
        DB::table('provinces')->insert(array('province' =>'Melilla','code' =>'52'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provinces');
    }
}
