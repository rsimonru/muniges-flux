<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsersSchedule extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * Get users permissions
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['user', 'schedule'],
        string $vcKeyBy = 'id'
    ) {

        $oQuery = static::select('users_schedules.*')
            ->join('users as u', 'u.id', 'users_schedules.users_id')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('users_schedules.id', $model_id);
            })
            ->when(isset($filters['users_id']) && ! empty($filters['users_id']), function ($query) use ($filters) {
                return $query->where('users_schedules.users_id', $filters['users_id']);
            })
            ->when(isset($filters['schedules_id']) && ! empty($filters['schedules_id']), function ($query) use ($filters) {
                return $query->where('users_schedules.schedules_id', $filters['schedules_id']);
            })
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) {
                return $query->whereExists(function ($query2) {
                    $query2->selectRaw(1)
                        ->from('users_town_halls as uth')
                        ->where('uth.townhalls_id', session('townhall_id'))
                        ->whereColumn('uth.users_id', 'users_schedules.users_id');
                });
            });
        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // dd($oQuery->toSql());
        return static::getModelData($oQuery, $model_id, $records_in_page, $with, $vcKeyBy);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'users_id');
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class, 'id', 'schedules_id');
    }
}
