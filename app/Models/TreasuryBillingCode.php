<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class TreasuryBillingCode extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'payment_date' => 'datetime',
        'expiration_date' => 'datetime',
        'r_date' => 'datetime',
    ];

    /**
     * Get services events
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['treasury_procedure', 'state']
    ) {

        $oQuery = static::select('treasury_billing_codes.*')
            ->join('treasury_procedures as p', 'p.id', 'treasury_billing_codes.procedures_id')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('treasury_billing_codes.id', $model_id);
            })
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
                return $query->where('treasury_billing_codes.townhalls_id', $filters['townhalls_id']);
            })
            ->when(isset($filters['procedures_id']) && ! empty($filters['procedures_id']), function ($query) use ($filters) {
                return $query->where('treasury_billing_codes.procedures_id', $filters['procedures_id']);
            })
            ->when(isset($filters['procedures_ids']) && ! empty($filters['procedures_ids']), function ($query) use ($filters) {
                return $query->whereIn('treasury_billing_codes.procedures_id', $filters['procedures_ids']);
            })
            ->when(isset($filters['states_ids']) && ! empty($filters['states_ids']), function ($query) use ($filters) {
                return $query->whereIn('treasury_billing_codes.states_id', $filters['states_ids']);
            })
            ->when(isset($filters['code']) && ! empty($filters['code']), function ($query) use ($filters) {
                return $query->where('treasury_billing_codes.code', $filters['code']);
            })
            ->when(isset($filters['vat']) && ! empty($filters['vat']), function ($query) use ($filters) {
                return $query->where('treasury_billing_codes.vat', $filters['vat']);
            })
            ->when(isset($filters['amount']) && ! empty($filters['amount']), function ($query) use ($filters) {
                return $query->where('treasury_billing_codes.amount', $filters['amount']);
            })
            ->when(isset($filters['date']) && isset($filters['datetype']) && ! empty($filters['date']) && ! empty($filters['datetype']), function ($query) use ($filters) {
                return $query->whereBetween('treasury_billing_codes.'.$filters['datetype'], $filters['date']);
            })
            ->when(isset($filters['search']) && ! empty($filters['search']), function ($query) use ($filters) {
                $query->where(function ($query2) use ($filters) {
                    $query2->where('treasury_billing_codes.code', 'like', '%'.$filters['search'].'%')
                        ->orWhere('treasury_billing_codes.vat', 'like', '%'.$filters['search'].'%')
                        ->orWhere('treasury_billing_codes.thirdparty', 'like', '%'.$filters['search'].'%');
                });

            })
            ->when(isset($filters['payment_data']) && ! empty($filters['payment_data']), function ($query) use ($filters) {
                $query->where('treasury_billing_codes.payment_data', 'like', '%'.$filters['payment_data'].'%');
            })
            ->when(isset($filters['created_at']) && ! empty($filters['created_at']), function ($query) use ($filters) {
                return $query->whereBetween('treasury_billing_codes.created_at', $filters['created_at']);
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        // $oQuery->dd();
        if (isset($filters['code']) && ! empty($filters['code']) && isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']) && length($filters) == 2) {
            $model_id = 1;
        }

        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    /**
     * Get services events
     *
     * @param  int  $model_id
     * @param  int  $records_in_page
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGetBankMovements(
        array $filters = []
    ) {

        $oQuery = static::from('treasury_billing_codes as c')
            ->select(
                'c.id', 'c.vat', 'c.thirdparty', 'c.procedures_id', 'model', 'models_id', 'c.record',
                'liquidation', 'observations', 'states_id', 'payment_data', 'payment_date',
                'expiration_date', 'c.townhalls_id', 'c.phase_r', 'c.r_date',
                'passport', 'c.code', 'p.description as procedure', 'y.code as contable_code',
                'c.amount', 'm.accounting_date', 'm.value_date', 'm.concept', 'm.balance', 'a.ordinal', 'c.created_at', 'c.updated_at'
            )
            ->join('treasury_procedures as p', 'p.id', 'c.procedures_id')
            ->join('treasury_procedures_years as y', 'y.procedures_id', 'p.id')
            ->leftJoin('treasury_banks_accounts_movements as m', 'm.bcodes_id', 'c.id')
            ->leftJoin('treasury_banks_accounts as a', 'a.id', 'm.accounts_id')
            ->where('y.year', date('Y'))
            ->where('c.states_id', '<>', config('states.cancelled'))
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
                return $query->where('c.townhalls_id', $filters['townhalls_id']);
            })
            ->when(isset($filters['date']) && ! empty($filters['date']), function ($query) use ($filters) {
                return $query->whereBetween('c.payment_date', $filters['date']);
            });
        $oQuery2 = DB::table('treasury_banks_accounts_movements as m')
            ->selectRaw('
            m.id, null as vat, null as thirdparty, 0 as procedures_id, null as `model`, 0 as models_id, null as `record`,
            null as liquidation, null as observations, 0 as states_id, null as payment_data, m.accounting_date as payment_date,
            null as expiration_date, a.townhalls_id, null as phase_r, null as r_date,
            null as passport, null as `code`, null as `procedure`, null as contable_code,
            m.amount, m.accounting_date, m.value_date, m.concept, m.balance, a.ordinal, m.created_at, m.updated_at')
            ->join('treasury_banks_accounts as a', 'a.id', 'm.accounts_id')
            ->where('m.bcodes_id', 0)
            ->where('m.amount', '>', 0)
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
                return $query->where('a.townhalls_id', $filters['townhalls_id']);
            })
            ->when(isset($filters['date']) && ! empty($filters['date']), function ($query) use ($filters) {
                return $query->whereBetween('m.accounting_date', $filters['date']);
            })
            ->union($oQuery);

        $oRecords = $oQuery2->get();
        $oRecords = $oRecords->sortBy('payment_date');

        return $oRecords;
    }

    public function save($options = [], $do_log = true)
    {
        if (empty($this->id)) {
            $this->code = TreasuryBillingCodesSequential::getCode(session('townhall_id'));
        }
        parent::save($options); // Calls Default Save
    }

    public function treasury_procedure()
    {
        return $this->hasOne(TreasuryProcedure::class, 'id', 'procedures_id')->with('procedure_year');
    }

    public function state()
    {
        return $this->hasOne(State::class, 'id', 'states_id');
    }
}
