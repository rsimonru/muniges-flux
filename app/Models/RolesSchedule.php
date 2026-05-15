<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Devlab\LaravelLogs\Traits\WithExtensions;

class RolesSchedule extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * Get group schedules
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
        array $with = ['group', 'schedule'],
        string $vcKeyBy = 'id'
    ) {

        $oQuery = static::select('groups_schedules.*')
        ->join('groups as g', 'g.id', 'groups_menus.groups_id')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('groups_schedules.id', $model_id);
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('g.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['groups_id']) && !empty($filters['groups_id']), function($query) use ($filters) {
            return $query->where('groups_schedules.groups_id', $filters['groups_id']);
        })
        ->when(isset($filters['schedules_id']) && !empty($filters['schedules_id']), function($query) use ($filters) {
            return $query->where('groups_schedules.schedules_id', $filters['schedules_id']);
        })
        ;
        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //dd($oQuery->toSql());
        return static::getModelData($oQuery, $model_id, $records_in_page, $with, $vcKeyBy);
    }

    public function group() {
        return $this->hasOne(Group::class, 'id', 'groups_id');
    }
    public function schedule() {
        return $this->hasOne(Schedule::class, 'id', 'schedules_id');
    }
}
