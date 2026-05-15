<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateStaffFreedaysReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_freedays_reasons', function (Blueprint $table) {
            $table->id();
            $table->json('description');
            $table->unsignedBigInteger('colors_id');
            $table->timestamps();
        });

        DB::table('staff_freedays_reasons')->insert(
            array('description' => json_encode(['es' => 'Vacaciones', 'en' => 'Holidays']),'colors_id' => 2)
        );
        DB::table('staff_freedays_reasons')->insert(
            array('description' => json_encode(['es' => 'Asuntos propios', 'en' => 'Personal affairs']),'colors_id' => 1)
        );
        DB::table('staff_freedays_reasons')->insert(
            array('description' => json_encode(['es' => 'Baja', 'en' => 'Down']),'colors_id' => 4)
        );
        DB::table('staff_freedays_reasons')->insert(
            array('description' => json_encode(['es' => 'Visita médica', 'en' => 'Medical visit']),'colors_id' => 6)
        );
        DB::table('staff_freedays_reasons')->insert(
            array('description' => json_encode(['es' => 'Vacaciones año anterior', 'en' => 'Last year holidays']),'colors_id' => 3)
        );
        DB::table('staff_freedays_reasons')->insert(
            array('description' => json_encode(['es' => 'Otros', 'en' => 'Others']),'colors_id' => 5)
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_freedays_reasons');
    }
}
