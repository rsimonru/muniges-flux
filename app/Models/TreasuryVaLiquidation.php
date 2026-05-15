<?php

namespace App\Models;

use App\Classes\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Devlab\LaravelLogs\Traits\WithExtensions;

class TreasuryVaLiquidation extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'document_date' => 'datetime',
        'transmission_date' => 'datetime',
        'previous_date' => 'datetime',
        'calculations' => 'json',
    ];

    /**
     * Get liquidations
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
        array $with = ['treasury_va_liquidations_type', 'treasury_va_liquidations_dtype', 'treasury_billing_code']
    ) {

        $oQuery = static::select('treasury_va_liquidations.*')
        ->leftJoin('treasury_billing_codes as bc', function ($join) {
            $join->on('bc.code','treasury_va_liquidations.billing_code')
            ->whereColumn('bc.townhalls_id','treasury_va_liquidations.townhalls_id');
        })
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('treasury_va_liquidations.id', $model_id);
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('treasury_va_liquidations.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['types_id']) && !empty($filters['types_id']), function($query) use ($filters) {
            return $query->where('treasury_va_liquidations.types_id', $filters['types_id']);
        })
        ->when(isset($filters['types_ids']) && !empty($filters['types_ids']), function($query) use ($filters) {
            return $query->whereIn('treasury_va_liquidations.types_id', $filters['types_ids']);
        })
        ->when(isset($filters['states_ids']) && !empty($filters['states_ids']), function($query) use ($filters) {
            return $query->whereIn('bc.states_id', $filters['states_ids']);
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            $query->where(function($query2) use ($filters) {
                $query2->where('treasury_va_liquidations.acq_name', 'like', '%'.$filters['search'].'%')
                ->orWhere('treasury_va_liquidations.acq_vat', 'like', '%'.$filters['search'].'%')
                ->orWhere('treasury_va_liquidations.tra_name', 'like', '%'.$filters['search'].'%')
                ->orWhere('treasury_va_liquidations.tra_vat', 'like', '%'.$filters['search'].'%');
            });
        })
        ->when(isset($filters['created_at']) && !empty($filters['created_at']), function ($query) use ($filters) {
            return $query->whereBetween('treasury_va_liquidations.created_at', $filters['created_at']);
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function save($params = array(), $options = array(), $do_log = true)
    {
        $new = false;
        if ($this->id>0) {
            $bcode = TreasuryBillingCode::emtGet(0,-1,[],[
                'townhalls_id' => session('townhall_id'),
                'code' => $this->billing_code,
            ]);
            if ($bcode && $bcode->amount<>$params['amount'] && $bcode->states_id == config('states.pending')) {
                $bcode->amount = $params['amount'];
                $bcode->save();
            }
            parent::save($options); // Calls Default Save
            return $this->id;
        } else {
            $new = true;
            $this->billing_code = '';
            parent::save($options); // Calls Default Save
            $type = $this->treasury_va_liquidations_ltype;
            if ($new) {
                // New billing code
                $bcode = new TreasuryBillingCode();
                $bcode->townhalls_id = session('townhall_id');
                $bcode->vat = $this->acq_vat;
                $bcode->passport = 0;
                $bcode->thirdparty = $this->acq_name;
                $bcode->procedures_id = $type->procedures_id;
                $bcode->model = get_class($this);
                $bcode->models_id = $this->id;
                $bcode->liquidation = $this->id;
                $bcode->record = '';
                $bcode->observations = $type->description;
                $bcode->states_id = config('states.pending');
                $bcode->amount = $params['amount'];
                $bcode->save();

                $this->billing_code = $bcode->code;
                parent::save($options); // Calls Default Save
            }
            return $this->id;
        }
    }

    public function delete($do_log = true) {
        TreasuryBillingCode::where('code', $this->billing_code)
            ->where('townhalls_id', session('townhall_id'))->delete();
        parent::delete();
    }

    public function treasury_va_liquidations_ltype()
    {
        return $this->hasOne(TreasuryLiquidationsType::class, 'id', 'ltypes_id');
    }
    public function treasury_va_liquidations_type()
    {
        return $this->hasOne(TreasuryVaLiquidationsType::class, 'id', 'types_id');
    }
    public function treasury_va_liquidations_dtype()
    {
        return $this->hasOne(TreasuryVaLiquidationsDtype::class, 'id', 'dtypes_id');
    }
    public function treasury_billing_code() {
        return $this->hasOne(TreasuryBillingCode::class, 'code', 'billing_code')
        ->where('treasury_billing_codes.townhalls_id',session('townhall_id'))
        ->with('state');
    }
}
