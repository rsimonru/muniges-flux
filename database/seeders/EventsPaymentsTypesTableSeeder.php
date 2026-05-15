<?php

namespace Database\Seeders;

use App\Models\EventsPaymentsType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class EventsPaymentsTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Inscripción 100%', 'en' => 'Inscription 100%']), ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Inscripción parcial', 'en' => 'Partial inscription']), ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Periódico', 'en' => 'Periodic']), ],
        ];

        EventsPaymentsType::upsert($records, ['id'], ['description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
