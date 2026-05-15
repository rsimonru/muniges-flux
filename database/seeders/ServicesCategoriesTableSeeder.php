<?php

namespace Database\Seeders;

use App\Models\ServicesCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ServicesCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Alumbrado', 'en' => 'Lighting']), ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Basuras', 'en' => 'Garbagge']), ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Mobiliario urbano', 'en' => 'Urban furniture ']), ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Jardines', 'en' => 'Gardens']), ],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Calles', 'en' => 'Streets']), ],
        ];

        ServicesCategory::upsert($records, ['id'], ['description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
