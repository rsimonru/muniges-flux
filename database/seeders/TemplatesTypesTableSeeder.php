<?php

namespace Database\Seeders;

use App\Models\TemplatesType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TemplatesTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => 'e-mail', ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => 'Documento', ],
        ];

        TemplatesType::upsert($records, ['id'], ['description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
