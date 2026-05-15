<?php

namespace Database\Seeders;

use App\Models\StatesModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class StatesModelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'states_id' => 1, 'model' => 'App\Models\TreasuryBillingCode', 'order' => 1, ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'states_id' => 2, 'model' => 'App\Models\TreasuryBillingCode', 'order' => 2, ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'states_id' => 5, 'model' => 'App\Models\TreasuryBillingCode', 'order' => 3, ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'states_id' => 2, 'model' => 'App\Models\ServicesEvent', 'order' => 2, ],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), 'states_id' => 3, 'model' => 'App\Models\ServicesEvent', 'order' => 1, ],
            ["id" => 6, "created_at" => now(), "updated_at" => now(), 'states_id' => 4, 'model' => 'App\Models\ServicesEvent', 'order' => 3, ],
            ["id" => 7, "created_at" => now(), "updated_at" => now(), 'states_id' => 6, 'model' => 'App\Models\Contact', 'order' => 1, ],
            ["id" => 8, "created_at" => now(), "updated_at" => now(), 'states_id' => 7, 'model' => 'App\Models\Contact', 'order' => 2, ],
            ["id" => 9, "created_at" => now(), "updated_at" => now(), 'states_id' => 6, 'model' => 'App\Models\StaffEmployee', 'order' => 1, ],
            ["id" => 10, "created_at" => now(), "updated_at" => now(), 'states_id' => 7, 'model' => 'App\Models\StaffEmployee', 'order' => 2, ],
            ["id" => 11, "created_at" => now(), "updated_at" => now(), 'states_id' => 6, 'model' => 'App\Models\SportsEventsRegistration', 'order' => 1, ],
            ["id" => 12, "created_at" => now(), "updated_at" => now(), 'states_id' => 7, 'model' => 'App\Models\SportsEventsRegistration', 'order' => 2, ],
            ["id" => 13, "created_at" => now(), "updated_at" => now(), 'states_id' => 1, 'model' => 'App\Models\SportsEventsRegistrationsPayment', 'order' => 1, ],
            ["id" => 14, "created_at" => now(), "updated_at" => now(), 'states_id' => 2, 'model' => 'App\Models\SportsEventsRegistrationsPayment', 'order' => 2, ],
            ["id" => 15, "created_at" => now(), "updated_at" => now(), 'states_id' => 1, 'model' => 'App\Models\TreasuryLiquidation', 'order' => 1, ],
            ["id" => 16, "created_at" => now(), "updated_at" => now(), 'states_id' => 2, 'model' => 'App\Models\TreasuryLiquidation', 'order' => 2, ],
            ["id" => 17, "created_at" => now(), "updated_at" => now(), 'states_id' => 5, 'model' => 'App\Models\TreasuryLiquidation', 'order' => 3, ],
            ["id" => 18, "created_at" => now(), "updated_at" => now(), 'states_id' => 1, 'model' => 'App\Models\TreasuryVaLiquidation', 'order' => 1, ],
            ["id" => 19, "created_at" => now(), "updated_at" => now(), 'states_id' => 2, 'model' => 'App\Models\TreasuryVaLiquidation', 'order' => 2, ],
            ["id" => 20, "created_at" => now(), "updated_at" => now(), 'states_id' => 5, 'model' => 'App\Models\TreasuryVaLiquidation', 'order' => 3, ],
            ["id" => 21, "created_at" => now(), "updated_at" => now(), 'states_id' => 1, 'model' => 'App\Models\ShowsTicket', 'order' => 1, ],
            ["id" => 22, "created_at" => now(), "updated_at" => now(), 'states_id' => 8, 'model' => 'App\Models\ShowsTicket', 'order' => 2, ],
            ["id" => 23, "created_at" => now(), "updated_at" => now(), 'states_id' => 10, 'model' => 'App\Models\ShowsTicket', 'order' => 3, ],
            ["id" => 24, "created_at" => now(), "updated_at" => now(), 'states_id' => 6, 'model' => 'App\Models\EventsRegistration', 'order' => 1, ],
            ["id" => 25, "created_at" => now(), "updated_at" => now(), 'states_id' => 7, 'model' => 'App\Models\EventsRegistration', 'order' => 2, ],
            ["id" => 26, "created_at" => now(), "updated_at" => now(), 'states_id' => 1, 'model' => 'App\Models\EventsRegistrationsPayment', 'order' => 1, ],
            ["id" => 27, "created_at" => now(), "updated_at" => now(), 'states_id' => 2, 'model' => 'App\Models\EventsRegistrationsPayment', 'order' => 2, ],
        ];

        StatesModel::upsert($records, ['id'], ['states_id', 'model', 'order', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
