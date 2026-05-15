<?php

namespace Database\Seeders;

use App\Models\CementeryServicesType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CementeryServicesTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Inhumación', 'en' => 'Inhumation']), ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Exhumación', 'en' => 'Exhumation']), ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Reinhumación', 'en' => 'Reburial']), ],
        ];

        CementeryServicesType::upsert($records, ['id'], ['description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
