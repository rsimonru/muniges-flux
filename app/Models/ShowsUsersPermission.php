<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShowsUsersPermission extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * Get users
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = []
    ) {

        $oQuery = static::select('shows_users_permissions.*')
            ->join('users as u', 'u.id', 'shows_users_permissions.users_id')
            ->join('shows as s', 's.id', 'shows_users_permissions.shows_id')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('shows_users_permissions.id', $model_id);
            })
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
                return $query->where('s.townhalls_id', $filters['townhalls_id']);
            })
            ->when(isset($filters['users_id']) && ! empty($filters['users_id']), function ($query) use ($filters) {
                return $query->where('shows_users_permissions.users_id', $filters['users_id']);
            })
            ->when(isset($filters['shows_id']) && ! empty($filters['shows_id']), function ($query) use ($filters) {
                return $query->where('shows_users_permissions.shows_id', $filters['shows_id']);
            })
            ->when(isset($filters['name']) && ! empty($filters['name']), function ($query) use ($filters) {
                return $query->where('u.name', 'like', '%'.$filters['name'].'%');
            })
            ->when(isset($filters['active']), function ($query) {
                return $query->where('s.from_date', '<=', today())
                    ->where('s.to_date', '>=', today());
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'users_id');
    }

    public function show()
    {
        return $this->hasOne(Show::class, 'id', 'shows_id');
    }
}
