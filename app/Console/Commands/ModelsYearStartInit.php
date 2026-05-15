<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TreasuryBillingCodesSequential;
use App\Models\TreasuryProceduresYear;
use Carbon\Carbon;

class ModelsYearStartInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'models:year-start-init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Init some models';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        TreasuryBillingCodesSequential::where('id', '>=', 1)
        ->update([
            'sequential' => 101,
        ]);

        $year = today()->format('Y')*1;
        $now = now();
        $procedures = TreasuryProceduresYear::where('year', $year-1)->get();
        $newprocedures = $procedures->map(function (TreasuryProceduresYear $model) use ($year, $now) {
            return [
                'procedures_id' => $model->procedures_id,
                'year' => $year,
                'code' => $model->code,
                'created_at' => $now,
            ];
        });
        TreasuryProceduresYear::insert($newprocedures->toArray());

        return 0;
    }
}
