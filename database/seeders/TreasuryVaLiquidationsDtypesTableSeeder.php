<?php

namespace Database\Seeders;

use App\Models\TreasuryVaLiquidationsDtype;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TreasuryVaLiquidationsDtypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Público', 'en' => 'Public']), ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Privado', 'en' => 'Private']), ],
        ];

        TreasuryVaLiquidationsDtype::upsert($records, ['id'], ['description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
