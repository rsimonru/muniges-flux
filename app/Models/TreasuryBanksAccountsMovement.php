<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Devlab\LaravelLogs\Traits\WithExtensions;

class TreasuryBanksAccountsMovement extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'accounting_date' => 'datetime',
        'value_date' => 'datetime',
        'r_date' => 'datetime',
    ];

    /**
     * Get bank accounts movements
     *
     * @param int $model_id
     * @param int $records_in_page
     * @param array $sort (attribute => 'asc'/'desc')
     * @param array $filters
     * @return mixed Collection
     *
     */
    public static function emtGet(
        int $model_id=0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['account','bcode']
    ) {

        $oQuery = static::select('treasury_banks_accounts_movements.*')
        ->join('treasury_banks_accounts as a', 'a.id', 'treasury_banks_accounts_movements.accounts_id')
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('a.townhalls_id', $filters['townhalls_id']);
        })
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('treasury_banks_accounts_movements.id', $model_id);
        })
        ->when(isset($filters['accounts_id']) && !empty($filters['accounts_id']), function($query) use ($filters) {
            return $query->where('treasury_banks_accounts_movements.accounts_id', $filters['accounts_id']);
        })
        ->when(isset($filters['accounts_ids']) && !empty($filters['accounts_ids']), function($query) use ($filters) {
            return $query->whereIn('treasury_banks_accounts_movements.accounts_id', $filters['accounts_ids']);
        })
        ->when(isset($filters['date']) && !empty($filters['date']), function($query) use ($filters) {
            return $query->whereBetween('treasury_banks_accounts_movements.accounting_date', $filters['date']);
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            $query->where(function($query2) use ($filters) {
                $query2->where('treasury_banks_accounts_movements.concept', 'like', '%'.$filters['search'].'%')
                ->orWhere('treasury_banks_accounts_movements.thirdparty', 'like', '%'.$filters['search'].'%');
            });

        })
        ;
        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    /**
     * Get last movement date
     *
     * @param array $filters
     * @return mixed Collection
     *
     */
    public static function emtGetLastMovementDate(
        array $filters = []
    ) {

        $date = static::
        join('treasury_banks_accounts as a', 'a.id', 'treasury_banks_accounts_movements.accounts_id')
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('a.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['accounts_id']) && !empty($filters['accounts_id']), function($query) use ($filters) {
            return $query->where('treasury_banks_accounts_movements.accounts_id', $filters['accounts_id']);
        })->max('accounting_date')
        ;
        //dd($oQuery->toSql());
        return $date;
    }

    public function account() {
        return $this->hasOne(TreasuryBanksAccount::class, 'id', 'accounts_id')->with('bank');
    }

    public function bcode() {
        return $this->hasOne(TreasuryBillingCode::class, 'id', 'bcodes_id')->with('treasury_procedure');
    }

}
