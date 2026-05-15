<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Devlab\LaravelLogs\Traits\WithExtensions;

class TreasuryBanksAccount extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'public' => 'boolean',
    ];

    protected $fillable = [
        'alias',
        'iban',
        'public',
        'banks_id',
        'townhalls_id',
        'n43_name',
    ];

    /**
     * Get bank accounts
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
        array $with = ['bank']
    ) {

        $oQuery = static::select('treasury_banks_accounts.*')
        ->selectRaw('concat(b.name, " - ", treasury_banks_accounts.alias) as account_name')
        ->when(isset($filters['date']) && !empty($filters['date']), function($query) {
            $query->selectRaw('
                count(*) as movements,
                sum(if(m.amount>0,1,0)) as income,
                sum(if(length(m.thirdparty)>0,1,0)) as completed
            ');
        })
        ->join('treasury_banks as b', function($join) {
            $join->on('treasury_banks_accounts.banks_id', 'b.id')
                //->whereNull('b.deleted_at')
                ;
        })
        ->when(isset($filters['date']) && !empty($filters['date']), function($query) {
            $query->leftjoin('treasury_banks_accounts_movements as m', function($join) {
                $join->on('treasury_banks_accounts.id', 'm.accounts_id')
                //->whereNull('m.deleted_at')
                ;
            });
        })
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('treasury_banks_accounts.id', $model_id);
        })
        ;

        $oQuery = static::dlApplyFilters($oQuery, $filters);

        $oQuery->groupBy('treasury_banks_accounts.id');
        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //dd($oQuery->toSql());

        if (isset($filters['n43_name']) && !empty($filters['n43_name']) && isset($filters['townhalls_id']) && !empty($filters['townhalls_id']) && length($filters)==2) {
            $model_id = 1;
        }
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }
    public static function dlApplyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('treasury_banks_accounts.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['accounts_ids']) && !empty($filters['accounts_ids']), function($query) use ($filters) {
            return $query->whereIn('treasury_banks_accounts.id', $filters['accounts_ids']);
        })
        ->when(isset($filters['n43_name']) && !empty($filters['n43_name']), function($query) use ($filters) {
            return $query->where('treasury_banks_accounts.n43_name', $filters['n43_name']);
        })
        ->when(isset($filters['banks_id']) && !empty($filters['banks_id']), function($query) use ($filters) {
            return $query->where('treasury_banks_accounts.banks_id', $filters['banks_id']);
        })
        ->when(isset($filters['public']) && !empty($filters['public']), function($query) {
            return $query->where('treasury_banks_accounts.public', 1);
        })
        ->when(isset($filters['date']) && !empty($filters['date']), function($query) use ($filters) {
            return $query->whereBetween('m.accounting_date', $filters['date']);
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            return $query->where(function ($query2) use ($filters) {
                $query2->where('treasury_banks_accounts.alias', 'like', '%'.$filters['search'].'%')
                ->orWhere('b.name', 'like', '%'.$filters['search'].'%')
                ->orWhere('treasury_banks_accounts.iban', 'like', '%'.$filters['search'].'%')
                ;
            });

        });

        return $oQuery;
    }

    public function bank() {
        return $this->hasOne(TreasuryBank::class, 'id', 'banks_id');
    }

}
