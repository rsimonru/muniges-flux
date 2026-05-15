<?php

namespace Database\Seeders;

use App\Models\ShowsTicketsType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ShowsTicketsTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'General', 'en' => 'General']), ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Protocolo', 'en' => 'Protocol']), ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Taquilla', 'en' => 'Ticket office']), ],
        ];

        ShowsTicketsType::upsert($records, ['id'], ['description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
