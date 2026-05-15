<?php

namespace Database\Seeders;

use App\Models\TemplatesSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TemplatesSectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => 'Pago', ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => 'Deportes', ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'description' => 'Tributos', ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'description' => 'Eventos', ],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), 'description' => 'Urbanismo', ],
            ["id" => 6, "created_at" => now(), "updated_at" => now(), 'description' => 'Genérica', ],
            ["id" => 7, "created_at" => now(), "updated_at" => now(), 'description' => 'General', ],
        ];

        TemplatesSection::upsert($records, ['id'], ['description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
