<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Devlab\LaravelLogs\Traits\WithExtensions;

class SportsInstallationsUsersPermission extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * Get users
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

        $oQuery = static::select('sports_installations_users_permissions.*')
        ->join('users as u', 'u.id', 'sports_installations_users_permissions.users_id')
        ->join('sports_installations as i', 'i.id', 'sports_installations_users_permissions.installations_id')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('sports_installations_users_permissions.id', $model_id);
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            $query->where('i.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['users_id']) && !empty($filters['users_id']), function($query) use ($filters) {
            return $query->where('sports_installations_users_permissions.users_id', $filters['users_id']);
        })
        ->when(isset($filters['installations_id']) && !empty($filters['installations_id']), function($query) use ($filters) {
            return $query->where('sports_installations_users_permissions.installations_id', $filters['installations_id']);
        })
        ->when(isset($filters['name']) && !empty($filters['name']), function($query) use ($filters) {
            return $query->where('u.name' , 'like', '%'.$filters['name'].'%');
        })
        ->when(isset($filters['active']), function ($query) {
            $query->whereExists(function ($query2) {
                $query2->selectRaw(1)
                    ->from('sports_installations_schedules as sh')
                    ->whereColumn('sh.installations_id', 'i.id')
                    ->where('sh.from_date', '<=', today())
                    ->where('sh.to_date', '>=', today());
            });
        });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function user() {
        return $this->hasOne(User::class, 'id', 'users_id');
    }
    public function sports_installation() {
        return $this->hasOne(SportsInstallation::class, 'id', 'installations_id');
    }
}
