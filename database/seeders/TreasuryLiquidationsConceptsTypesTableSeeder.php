<?php

namespace Database\Seeders;

use App\Models\TreasuryLiquidationsConceptsType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TreasuryLiquidationsConceptsTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Lineal', 'en' => 'Linear']), ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Tramo', 'en' => 'Interval']), ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Descripción', 'en' => 'Description']), ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Sección', 'en' => 'Section']), ],
        ];

        TreasuryLiquidationsConceptsType::upsert($records, ['id'], ['description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
