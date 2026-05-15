<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SportsInstallationsPass;
use App\Models\TreasuryBillingCode;
use Carbon\Carbon;

class SportsCancelPasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sports:cancel-passes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel expired passes';

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
        $ids = SportsInstallationsPass::select('id')
        ->where('expiration_date','<=', Carbon::now())
        ->where('states_id', config('states.pending'))
        ->get()->toArray();

        if (length($ids)>0) {
            TreasuryBillingCode::whereIn('models_id',$ids)
            ->where('model', 'App\Models\SportsInstallationsReservation')
            ->update([
                'states_id' => config('states.cancelled')
            ]);
            SportsInstallationsPass::whereIn('id',$ids)
            ->update([
                'states_id' => config('states.cancelled')
            ]);
        }

        return 0;
    }
}
