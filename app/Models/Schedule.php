<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['description'];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

    protected $fillable = ['description', 'townhalls_id', 'colors_id'];

    /**
     * Get schedules
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

        $oQuery = static::select('schedules.*')
            ->selectRaw('count(a.id) as appointments_number')
            ->leftJoin('schedules_appointments as a', 'a.schedules_id', 'schedules.id')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('schedules.id', $model_id);
            });

        $oQuery = static::dlApplyFilters($oQuery, $filters);

        $oQuery->groupBy('schedules.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // dd($oQuery->toSql());
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public static function dlApplyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
            return $query->where('schedules.townhalls_id', $filters['townhalls_id']);
        })
            ->when(isset($filters['description']) && ! empty($filters['description']), function ($query) use ($filters) {
                $query->whereRaw('lower(schedules.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['description']).'%"');
            })
            ->when(isset($filters['search']) && ! empty($filters['search']), function ($query) use ($filters) {
                return $query->where(function ($query) use ($filters) {
                    $query->whereRaw('lower(schedules.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['search']).'%"');
                });
            });

        return $oQuery;
    }

    /**
     * Get user schedules
     *
     * @param  int  $user_id
     * @return mixed Collection
     */
    public static function emtGetUser(
        int $iUsers_id
    ) {

        $oQuery = static::select('schedules.*')
            ->leftjoin('users_schedules as us', 'us.schedules_id', 'schedules.id')
            ->leftjoin('roles_schedules as gs', 'gs.schedules_id', 'schedules.id')
            ->leftjoin('users_groups as ug', 'ug.groups_id', 'gs.groups_id')
            ->where(function ($query2) use ($iUsers_id) {
                return $query2->where('us.users_id', $iUsers_id)
                    ->orWhere('ug.users_id', $iUsers_id);
            })
            ->where('schedules.townhalls_id', session('townhall_id'))
            ->distinct()
            ->orderBy('schedules.description->'.app()->getLocale(), 'asc');

        return static::getModelData($oQuery, 0, -1, []);
    }

    public function color()
    {
        return $this->hasOne(Color::class, 'id', 'colors_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, UsersSchedule::class, 'schedules_id', 'users_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Role::class, RolesSchedule::class, 'schedules_id', 'role_id');
    }
}
