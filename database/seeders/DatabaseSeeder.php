<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CementeryServicesTypesTableSeeder::class);
        $this->call(ColorsTableSeeder::class);
        $this->call(EventsPaymentsTypesTableSeeder::class);
        $this->call(FormsAreasTableSeeder::class);
        $this->call(HelpsUrlTypesTableSeeder::class);
        $this->call(LevelsTableSeeder::class);
        $this->call(MenusTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(ProvincesTableSeeder::class);
        $this->call(ServicesCategoriesTableSeeder::class);
        $this->call(ShowsTicketsTypesTableSeeder::class);
        $this->call(SportsEventsPaymentsTypesTableSeeder::class);
        $this->call(SportsInstallationsGroupsSlotsTableSeeder::class);
        $this->call(StaffFreedaysReasonsTableSeeder::class);
        $this->call(StatesModelsTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(TemplatesObjectsTableSeeder::class);
        $this->call(TemplatesSectionsTableSeeder::class);
        $this->call(TemplatesTypesTableSeeder::class);
        $this->call(TreasuryLiquidationsConceptsTypesTableSeeder::class);
        $this->call(TreasuryVaLiquidationsDtypesTableSeeder::class);
        $this->call(TreasuryVaLiquidationsTypesTableSeeder::class);

    }
}
