<?php

namespace Database\Seeders;

use App\Models\SportsInstallationsGroupsSlot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SportsInstallationsGroupsSlotsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'value' => '30','description' => json_encode(['es' => '30 minutos', 'en' => '30 minutes']), ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'value' => '100','description' => json_encode(['es' => '1 hora', 'en' => '1 hour']), ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'value' => '-1','description' => json_encode(['es' => 'Turno', 'en' => 'Turn']), ],
        ];

        SportsInstallationsGroupsSlot::upsert($records, ['id'], ['description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
