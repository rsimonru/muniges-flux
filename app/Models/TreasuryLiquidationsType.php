<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TreasuryLiquidationsType extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
    ];

    public $translatable = ['description', 'information', 'warning', 'extra_field'];

    /**
     * Get liquidations
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['treasury_liquidations_concepts']
    ) {

        $oQuery = static::select('treasury_liquidations_types.*')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('treasury_liquidations_types.id', $model_id);
            })
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
                return $query->where('treasury_liquidations_types.townhalls_id', $filters['townhalls_id']);
            })
            ->when(isset($filters['description']) && ! empty($filters['description']), function ($query) use ($filters) {
                return $query->whereRaw('lower(treasury_liquidations_types.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['description']).'%"');
            })
            ->when(isset($filters['active']), function ($query) {
                return $query->where('treasury_liquidations_types.from_date', '<=', Carbon::now())
                    ->where('treasury_liquidations_types.to_date', '>=', Carbon::now())
                    ->where('is_capital_gain', 0);
            })
            ->when(isset($filters['is_capital_gain']) && ! empty($filters['is_capital_gain']), function ($query) use ($filters) {
                return $query->where('treasury_liquidations_types.is_capital_gain', $filters['is_capital_gain']);
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function treasury_liquidations_concepts()
    {
        return $this->hasMany(TreasuryLiquidationsConcept::class, 'ltypes_id', 'id')
            ->with('treasury_liquidations_concepts_type');
    }
}
