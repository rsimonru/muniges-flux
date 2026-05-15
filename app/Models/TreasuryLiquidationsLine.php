<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Devlab\LaravelLogs\Traits\WithExtensions;

class TreasuryLiquidationsLine extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * Get liquidation lines
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
        array $with = ['treasury_liquidations_concept']
    ) {

        $oQuery = static::select('treasury_liquidations_lines.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('treasury_liquidations_lines.id', $model_id);
        })
        ->when(isset($filters['liquidations_id']) && !empty($filters['liquidations_id']), function($query) use ($filters) {
            return $query->where('treasury_liquidations_lines.liquidations_id', $filters['liquidations_id']);
        })
        ->when(isset($filters['ctypes_id']) && !empty($filters['ctypes_id']), function($query) use ($filters) {
            return $query->where('treasury_liquidations_lines.ctypes_id', $filters['ctypes_id']);
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function treasury_liquidations_concept()
    {
        return $this->hasOne(TreasuryLiquidationsConcept::class, 'id', 'concepts_id');
    }
}
