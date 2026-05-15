<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\TreasuryBillingCode;
use App\Models\TreasuryBanksAccountsMovement;
use Carbon\Carbon;
use App\Events\AccountMovementFinishImport;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AccountMovementImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $movements;
    public $accounts;
    public $fields_columns;
    public $users_id;
    public $townhalls_id;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct($users_id, $townhalls_id, $movements, $accounts, $fields_columns)
	{
		//
        $this->users_id = $users_id;
        $this->townhalls_id = $townhalls_id;
		$this->movements = $movements;
        $this->accounts = $accounts;
        $this->fields_columns = $fields_columns;
	}

    private function saveMovement($movement, $pcodes, $acodes, $account) {
        $states_id = config('states.payed');
        $contains = emt_contains($movement['concept'],$acodes);
        //Log::info('contains: '. json_encode($contains));
        if (isset($contains[0]) && !empty($contains[0])) {
            $pcode = $pcodes[strtoupper($contains[0])];
            $pcode->payment_data = $account->bank->name.' - '.$account->alias.' ('.$movement['accounting_date']->format('d/m/Y').')';
            $pcode->states_id = $states_id;
            $pcode->payment_date = $movement['accounting_date'];
            $pcode->save();

            $movement['bcodes_id'] = $pcode->id;
            $movement['vat']=$pcode->vat;
            $movement['thirdparty']=$pcode->thirdparty;
            $movement['record']=$pcode->record;
            $movement['internal_concept']=$pcode->observations;
            $movement['more_info']=$pcode->liquidation;
            $movement['phase_r']=$pcode->phase_r;
            $movement['r_date']=$pcode->r_date;
        }
        return $movement;
    }

    /**
     * Handle the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(120);
        $last_month = 0;
        $pcodes = new TreasuryBillingCode();
        $movements = [];
        $now = now();
        $states = config('states');
        foreach ($this->movements as $key => $row) {
            $date = new Carbon($row[$this->fields_columns['accounting_date']]);
            $pcodes_from = $date->clone()->startOfMonth()->subDays(90);
            $pcodes_to = $date->clone()->endOfMonth();
            if ($last_month != $date->month) {
                $pcodes = TreasuryBillingCode::emtGet(0,-1, [], [
                    'townhalls_id' => $this->townhalls_id,
                    'datetype' => 'created_at',
                    'date' => [$pcodes_from, $pcodes_to],
                    'states_ids' => [$states['payed'], $states['pending']],
                ]);
                $last_month = $date->month;
                $pcodes = $pcodes->keyBy('code');
                $acodes = $pcodes->keys()->toArray();
            }
            $movement = [];
            $movement['accounts_id'] = ($this->fields_columns['accounts_id'] == '#file#') ? $row['accounts_id']:$this->fields_columns['accounts_id'];
            $movement['accounting_date'] = $row[$this->fields_columns['accounting_date']];
            $movement['value_date'] = $row[$this->fields_columns['value_date']];
            foreach($this->fields_columns['concept'] as $col_key) {
                $movement['concept'] = ($movement['concept']??'').' '.trim($row[$col_key]);
            }
            $movement['amount'] = $row[$this->fields_columns['amount']] * 1;
            $movement['balance'] = $row[$this->fields_columns['balance']] * 1;
            $movement['bcodes_id'] = 0;
            $movement['vat']='';
            $movement['thirdparty']='';
            $movement['record']='';
            $movement['internal_concept']='';
            $movement['more_info']='';
            $movement['phase_r']='';
            $movement['r_date']=null;
            $movement['created_at'] = $now;

            $movements[] = $this->saveMovement($movement, $pcodes, $acodes, $this->accounts[$movement['accounts_id']]);
        }
        TreasuryBanksAccountsMovement::insert($movements);
        event(new AccountMovementFinishImport($this->users_id));
    }
}
