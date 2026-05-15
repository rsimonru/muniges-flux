<?php

namespace Database\Seeders;

use App\Models\TreasuryVaLiquidationsType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TreasuryVaLiquidationsTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Compra-venta', 'en' => 'Buy and sell']), ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Herencia', 'en' => 'Inheritance']), ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Derecho real', 'en' => 'Real right']), ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Donación', 'en' => 'Donation']), ],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Aportación societaria', 'en' => 'Corporate contribution']), ],
            ["id" => 6, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Donación en pago', 'en' => 'Donation in payment ']), ],
            ["id" => 7, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Permuta', 'en' => 'Barter']), ],
            ["id" => 8, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Reducción societaria', 'en' => 'Corporate reduction']), ],
            ["id" => 9, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Extinción de condominio', 'en' => 'Condominium extintion']), ],
            ["id" => 10, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Expropiación', 'en' => 'Expropriation']), ],
            ["id" => 11, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Otros', 'en' => 'Others']), ],
        ];

        TreasuryVaLiquidationsType::upsert($records, ['id'], ['description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
