<?php

namespace Database\Seeders;

use App\Models\StaffFreedaysReason;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class StaffFreedaysReasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Vacaciones', 'en' => 'Holidays']),'colors_id' => 2, ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Asuntos propios', 'en' => 'Personal affairs']),'colors_id' => 1, ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Baja', 'en' => 'Down']),'colors_id' => 4, ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Visita médica', 'en' => 'Medical visit']),'colors_id' => 6, ],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Vacaciones año anterior', 'en' => 'Last year holidays']),'colors_id' => 3, ],
            ["id" => 6, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Otros', 'en' => 'Others']),'colors_id' => 5, ],
        ];

        StaffFreedaysReason::upsert($records, ['id'], ['description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
