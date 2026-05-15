<?php

namespace Database\Seeders;

use App\Models\FormsArea;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FormsAreasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Urbanismo', 'en' => 'Town planning']), ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Sanciones', 'en' => 'Penalties']), ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Deportes', 'en' => 'Sports']), ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Cultura', 'en' => 'Culture']), ],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Sanidad', 'en' => 'Health']), ],
            ["id" => 6, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'General', 'en' => 'General']), ],
        ];

        FormsArea::upsert($records, ['id'], ['description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
