<?php

namespace App\Models;

use App\Models\Scopes\TownHallScope;
use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasPermissions;

class Role extends Model
{
    use HasFactory;
    use HasPermissions;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['description'];

    public $fillable = [
        'name',
        'description',
        'level_id',
        'townhalls_id',
        'guard_name',
    ];

    public $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

    public $attributes = [
        'guard_name' => 'web',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TownHallScope);
    }

    /**
     * Get records
     *
     * @param int model_id
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = [],
        array $withCount = []
    ) {
        $oQuery = static::select('roles.*')
            ->join('levels as l', 'l.id', 'roles.level_id')
            ->when($model_id != 0, function ($query) use ($model_id) {
                return $query->where('roles.id', $model_id);
            })
            ->where('l.level', '<=', Auth::user()->level->level);

        $oQuery = static::dlApplyFilters($oQuery, $filters);

        $oQuery->groupBy('roles.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with, 'id', null, [], $withCount);
    }

    public static function dlApplyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['level']) && $filters['level'] > 0, function ($query) use ($filters) {
            return $query->where('l.level', '<=', $filters['level']);
        })
            ->when(isset($filters['levels']) && ! empty($filters['levels']), function ($query) use ($filters) {
                return $query->whereIn('roles.level_id', $filters['levels']);
            })
            ->when(isset($filters['min_level']) && ! empty($filters['min_level']), function ($query) use ($filters) {
                return $query->where('l.level', '>=', $filters['min_level']);
            })
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
                return $query->where('roles.townhalls_id', $filters['townhalls_id']);
            })
            ->when(isset($filters['level_id']) && ! empty($filters['level_id']), function ($query) use ($filters) {
                return $query->where('roles.level_id', $filters['level_id']);
            })
            ->when(isset($filters['description']) && ! empty($filters['description']), function ($query) use ($filters) {
                $query->whereRaw('lower(roles.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['description']).'%"');
            })
            ->when(isset($filters['search']) && ! empty($filters['search']), function ($query) use ($filters) {
                return $query->where(function ($query) use ($filters) {
                    $query->whereRaw('lower(roles.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['search']).'%"');
                });
            });

        return $oQuery;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id')
            ->join('roles', 'roles.id', 'model_has_roles.role_id')
            ->where('roles.townhalls_id', session('townhall_id'));
    }

    public function level()
    {
        return $this->hasOne(Level::class, 'id', 'level_id');
    }

    public function menus()
    {
        return $this->hasManyThrough(Permission::class, RoleHasPermission::class, 'role_id', 'id', 'id', 'permission_id')
            ->join('menus', 'menus.id', 'permissions.model_id')
            ->where('permissions.model', Menu::class)
            ->with('menu');
    }

    public function submenus()
    {
        return $this->hasManyThrough(Permission::class, RoleHasPermission::class, 'role_id', 'id', 'id', 'permission_id')
            ->join('menus', 'menus.id', 'permissions.model_id')
            ->where('permissions.model', Menu::class)
            ->whereColumn('menus.id', '<>', 'menus.pmenus_id')
            ->with('menu');
    }

    public function fsubmenus()
    {
        return $this->hasManyThrough(Permission::class, RoleHasPermission::class, 'role_id', 'id', 'id', 'permission_id')
            ->join('menus', 'menus.id', 'permissions.model_id')
            ->where('role_has_permissions.favorite', 1)
            ->where('permissions.model', Menu::class)
            ->whereColumn('menus.id', '<>', 'menus.pmenus_id')
            ->with('menu');
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, RolesSchedule::class, 'role_id', 'schedules_id')
            ->where('schedules.townhalls_id', session('townhall_id'));
    }

    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            app(PermissionRegistrar::class)->pivotRole,
            app(PermissionRegistrar::class)->pivotPermission
        );
    }
}
