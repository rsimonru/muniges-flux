<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Help extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * Get schedules
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
        array $with = ['type']
    ) {

        $oQuery = static::select('helps.*')
        ->join('helps_url_types as t', 't.id', 'helps.urltypes_id')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('helps.id', $model_id);
        });

        $oQuery = static::dlApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }
    /**
     * Apply filters.
     *
     * @param $oQuery
     * @param array $filters
     * @return mixed Query
     *
     */
    public static function dlApplyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['urltypes_id']) && !empty($filters['urltypes_id']), function($query) use ($filters) {
            return $query->where('helps.urltypes_id', $filters['urltypes_id']);
        })
        ->when(isset($filters['path']) && !empty($filters['path']), function($query) use ($filters) {
            $query->where('helps.path', 'like', '%'.$filters['path'].'%');
        });

        return $oQuery;
    }

    public function type() {
        return $this->hasOne(HelpsUrlType::class, 'id', 'urltypes_id');
    }

}
