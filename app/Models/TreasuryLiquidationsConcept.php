<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TreasuryLiquidationsConcept extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['description'];

    /**
     * Get liquidation concepts
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['treasury_liquidations_concepts_type']
    ) {

        $oQuery = static::select('treasury_liquidations_concepts.*')
            ->selectRaw('count(l.id) as lines_count')
            ->leftJoin('treasury_liquidations_lines as l', 'l.concepts_id', 'treasury_liquidations_concepts.id')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('treasury_liquidations_concepts.id', $model_id);
            })
            ->when(isset($filters['ltypes_id']) && ! empty($filters['ltypes_id']), function ($query) use ($filters) {
                return $query->where('treasury_liquidations_concepts.ltypes_id', $filters['ltypes_id']);
            })
            ->when(isset($filters['ctypes_id']) && ! empty($filters['ctypes_id']), function ($query) use ($filters) {
                return $query->where('treasury_liquidations_concepts.ctypes_id', $filters['ctypes_id']);
            });

        $oQuery->groupBy('treasury_liquidations_concepts.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function treasury_liquidations_concepts_type()
    {
        return $this->hasOne(TreasuryLiquidationsConceptsType::class, 'id', 'ctypes_id');
    }
}
