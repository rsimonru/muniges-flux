<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSportsInstallationsGroupsSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sports_installations_groups_slots', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('value')->unique();
            $table->json('description');
            $table->timestamps();
        });
        DB::table('sports_installations_groups_slots')->insert(
            array('value' => '30','description' => json_encode(['es' => '30 minutos', 'en' => '30 minutes']))
        );
        DB::table('sports_installations_groups_slots')->insert(
            array('value' => '100','description' => json_encode(['es' => '1 hora', 'en' => '1 hour']))
        );
        DB::table('sports_installations_groups_slots')->insert(
            array('value' => '-1','description' => json_encode(['es' => 'Turno', 'en' => 'Turn']))
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sports_installations_groups_slots');
    }
}
