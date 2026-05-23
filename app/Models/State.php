<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['description'];

    /**
     * Get state
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = []
    ) {

        $oQuery = static::select('states.*')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('states.id', $model_id);
            })
            ->when(isset($filters['states_ids']) && ! empty($filters['states_ids']), function ($query) use ($filters) {
                return $query->whereIn('states.id', $filters['states_ids']);
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page);
    }
}
