<?php

namespace Database\Seeders;

use App\Models\HelpsUrlType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class HelpsUrlTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => 'Intranet', ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => 'Pública', ],
        ];

        HelpsUrlType::upsert($records, ['id'], ['description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
