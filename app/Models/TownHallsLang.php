<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TownHallsLang extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * Get town hall langs
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
        array $with = ['townhall']
    ) {

        $oQuery = static::select('town_halls_langs.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('town_halls_langs.id', $model_id);
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            $query->where('town_halls_langs.townhalls_id', $filters['townhalls_id']);
        });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //dd($oQuery->toSql());
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function townhall() {
        return $this->hasOne(TownHall::class, 'id', 'townhalls_id');
    }
}
