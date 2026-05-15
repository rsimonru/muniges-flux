<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Devlab\LaravelLogs\Traits\WithExtensions;

class Device extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * Get devices
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
        array $with = []
    ) {

        $oQuery = static::select('devices.*')
        ->when($model_id!=0, function($query) use ($model_id) {
            return $query->where('devices.id', $model_id);
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('devices.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['platform']) && !empty($filters['platform']), function($query) use ($filters) {
            return $query->where('devices.platform', $filters['platform']);
        })
        ->when(isset($filters['lang']) && !empty($filters['lang']), function($query) use ($filters) {
            return $query->where('devices.lang', $filters['lang']);
        })
        ->when(isset($filters['other_langs']) && !empty($filters['other_langs']), function($query) use ($filters) {
            return $query->whereNotIn('devices.lang', $filters['other_langs']);
        })
        ->when(isset($filters['active']), function($query) use ($filters) {
            return $query->where('devices.active', $filters['active'])
            ->where('devices.device_token','<>','');
        })
        ->when(isset($filters['device_token']) && !empty($filters['device_token']), function($query) use ($filters) {
            return $query->where('devices.device_token', 'like', '%'.$filters['device_token'].'%');
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }
}
