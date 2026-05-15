<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Scopes\TownHallScope;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Devlab\LaravelLogs\Models\ModelsLog;
use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\PermissionRegistrar;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, WithExtensions;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime:d-m-Y H:i:s',
            'updated_at' => 'datetime:d-m-Y H:i:s',
            'last_login' => 'datetime:d-m-Y H:i:s',
            'filters' => 'array',
            'active' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    protected static function booted()
    {
        static::addGlobalScope(new TownHallScope);
    }

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

        $oQuery = static::select('users.*', 'uth.level_id', 'l.name as level_name', 'l.level as level_number')
        ->join('users_town_halls as uth', 'users.id', 'uth.users_id')
        ->join('levels as l', 'l.id', 'uth.level_id')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('users.id', $model_id);
        })
        ;

        $oQuery = static::dlApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
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

        $oQuery->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('uth.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['active']) && ($filters['active'] == 1 || $filters['active'] == 0), function($query) use ($filters) {
            return $query->where('users.active', $filters['active']);
        })
        ->when(isset($filters['level']) && !empty($filters['level']), function($query) use ($filters) {
            return $query->where('l.level', '<=', $filters['level']);
        })
        ->when(isset($filters['under_level']) && !empty($filters['under_level']), function($query) use ($filters) {
            return $query->where('l.level', '<', $filters['under_level']);
        })
        ->when(isset($filters['min_level']) && !empty($filters['min_level']), function($query) use ($filters) {
            return $query->where('l.level', '>=', $filters['min_level']);
        })
        ->when(isset($filters['max_level']) && !empty($filters['max_level']), function($query) use ($filters) {
            return $query->where('l.level', '<=', $filters['max_level']);
        })
        ->when(isset($filters['level_id']) && !empty($filters['level_id']), function($query) use ($filters) {
            return $query->where('uth.level_id',$filters['level_id']);
        })
        ->when(isset($filters['levels']) && !empty($filters['levels']), function($query) use ($filters) {
            return $query->whereIn('uth.level_id',$filters['levels']);
        })
        ->when(isset($filters['group_id']) && !empty($filters['group_id']), function($query) use ($filters) {
            return $query->whereHas('roles', function ($query) use ($filters) {
                $query->whereInto('roles.id', $filters['group_id']);
            });
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            return $query->where(function ($query) use ($filters) {
                $query->where('users.email', 'like', '%'.$filters['search'].'%')
                ->orWhere('users.name', 'like', '%'.$filters['search'].'%');
            });
        });

        return $oQuery;
    }

    /**
     * Get user filters.
     *
     * @return mixed aFilters array()
     *
     */
    public static function getFilters($key = '')
    {
        $oUser = User::find(Auth::user()->id);
        $aUFilters = $oUser->filters;
        $templates = config('filters');
        $flist = [
            'FilterOn' => 0,
            //'ModalName' => 'modal_Filter',
            'aValues' => [],
        ];
        $filters = [];
        $filters_defaults = [];

        if (!empty($key)) { // Si la clave del filtro no está vacía
            if (!isset($aUFilters[$key]) || empty($aUFilters[$key])) { // Si el usuario no tiene filtros para esa clave
                if (isset($templates[$key][0])) { // Si existe plantilla para esa clave
                    $aUFilters[$key] = $templates[$key][0];
                    $oUser->filters = $aUFilters;
                    $oUser->save();
                } else {
                    return array(); // No hay plantilla para esa clave
                }
            }

            $filters = $aUFilters;
            foreach ($templates[$key][1] as $key1 => $value) {
                $aValues = [];
                $label = '';
                if (isset($aUFilters[$key][$key1]) && $aUFilters[$key][$key1]) {
                    switch ($value[0]) {
                        case 'text':
                            $aValues = [$aUFilters[$key][$key1]];
                            $label = $value[2];
                            break;
                        case 'array':
                            $aValuesF = (is_array($aUFilters[$key][$key1])) ? $aUFilters[$key][$key1] : explode(',', $aUFilters[$key][$key1]);
                            if (is_array($value[1])) {
                                $aValues = array_intersect_key($value[1], array_combine(array_values($aValuesF), array_values($aValuesF)));
                            } else {
                                $aTable = explode(":", $value[1]);
                                $vcModel = $aTable[0];
                                $vcAttrib = $aTable[1];
                                if ($vcModel == 'Select') {
                                    $aTmpValues = Select::emtGet($vcAttrib);
                                    $aValues = [];
                                    foreach ($aTmpValues as $key2 => $value2) {
                                        $aValues[$value2['value']] = $value2['option'];
                                    }
                                    $aValues = array_intersect_key($aValues, array_combine(array_values($aValuesF), array_values($aValuesF)));
                                } else {
                                    $vcModel = "App\\Models\\" . $vcModel;
                                    $aTmpValues = $vcModel::selectRaw('id,`' . $vcAttrib . '` `value`')->get()->toArray();
                                    $aValues = [];
                                    foreach ($aTmpValues as $key2 => $value2) {
                                        $aValues[$value2['id']] = $value2['value'];
                                    }
                                    $aValues = array_intersect_key($aValues, array_combine(array_values($aValuesF), array_values($aValuesF)));
                                }
                            }
                            $label = $value[2];
                            break;
                        case 'date':
                            $date_from = new Carbon($aUFilters[$key][$key1][1]);
                            $date_to = new Carbon($aUFilters[$key][$key1][2]);
                            $label = config('constants.date_type.' . $aUFilters[$key][$key1][0]);
                            $aValues = [$label . ' de ' . $date_from->format('d-m-Y') . ' a ' . $date_to->format('d-m-Y')];
                            break;
                    }
                    if (!empty($aValues)) {
                        $flist['FilterOn'] = 1;
                    }
                    $flist['aValues'][$key1] = array(
                        'aValues' => $aValues,
                        'label' => $label
                    );
                }
            }
            // Valor por defecto para los filtros
            if (isset($templates[$key][2])) {
                foreach ($templates[$key][2] as $key1 => $value) {
                    if (isset($templates[$key][1][$key1])) {
                        if ($templates[$key][1][$key1][0] == 'date') {
                            $filters_defaults[$key][$key1]['aValues'] = $value;

                            $date_from = new Carbon($value[1]);
                            $date_to = new Carbon($value[2]);
                            $label = config('constants.date_type.' . $value[0]);
                            $filters_defaults[$key][$key1]['label'] = [$label . ' de ' . $date_from->format('d-m-Y') . ' a ' . $date_to->format('d-m-Y')];
                        }
                    }
                }
            }

            $result = [
                'flist' => $flist,
                'ufilters' => $aUFilters[$key],
                'filters' => $filters,
                'filters_defaults' => $filters_defaults,
            ];
            return $result;
        } else {
            return []; // Se ha pasado una clave de filtro vacía
        }
    }
    /**
     * Save user filters.
     *
     * @return mixed aResult(iResult, vcMessage)
     *
     */
    public static function saveFilters($key, $filters)
    {
        $oUser = Auth::user(); //User::find(auth()->user()->id);
        $aUFilters = $oUser->filters;
        $templates = config('filters');
        $aClean = array();

        if (!empty($filters) && !empty($key)) {
            foreach ($filters as $key1 => $value) {
                if ($key1 != '_token' && $key1 != 'page' && $key1 != 'signature') {
                    //$key = substr($key,strpos($key,'_')+1);
                    $aClean[$key1] = $value;
                }
            }
            $aDif = array_diff_key($templates[$key][0], $aClean);
            $aMerge = array_merge($aClean, $aDif);
            $aUFilters[$key] = $aMerge;
            $oUser->filters = $aUFilters;
            $oUser->save([], false);
            return ['iResult' => $oUser->id];
        } else {
            return ['iResult' => -1, 'vcMessage' => 'Clave o valores de filtro vacíos'];
        }
    }
    public static function saveFilterSort($key, $filters)
    {
        $oUser = Auth::user();
        if (!empty($filters) && !empty($key)) {
            $user_filters = $oUser->filters;
            $user_filters[$key]['sort'] = $filters['sort'];
            $user_filters[$key]['order'] = $filters['order'];
            $oUser->filters = $user_filters;
            $oUser->save([], false);
            return ['iResult' => $oUser->id];
        } else {
            return ['iResult' => -1, 'vcMessage' => 'Clave o valores de filtro vacíos'];
        }
    }
    /**
     * Reset user filters.
     *
     * @param int $iUsers_id
     * @param array $aAttributes array (attribute => value)
     * @return mixed aResult(iResult, vcMessage)
     *
     */
    public static function resetFilters($ids = [], $prefix = '')
    {
        $aResult = ['iResult' => 0, 'vcMessage' => ''];
        if (empty($prefix)) {
            User::where('id', '>', 0)
                ->when(!empty($ids), function ($query) use ($ids) {
                    return $query->whereIn('id', $ids);
                })
                ->update(['filters' => null]);
        } else {
            if (empty($ids)) {
                $ids = User::all()->keyBy('id')->keys()->all();
            }
            foreach ($ids as $id) {
                $user = User::find($id);
                $filters = $user->filters;
                if (!empty($filters)) {
                    $filters = array_filter($filters, function ($key) use ($prefix) {
                        return !str_starts_with($key, $prefix);
                    }, ARRAY_FILTER_USE_KEY);
                    User::where('id', $id)->update(['filters' => $filters]);
                }
            }
        }
        return $aResult;
    }

    public function roles() {
        return $this->belongsToMany(Role::class, ModelHasRole::class, 'model_id', 'role_id')
            ->where('model_has_roles.model_type', User::class)
            ->where('roles.townhalls_id', session('townhall_id'));
    }
    public function townhall() {
        return $this->hasOne(UsersTownHall::class, 'users_id', 'id')
            ->where('users_town_halls.townhalls_id', session('townhall_id'));
    }
    public function townhalls() {
        return $this->belongsToMany(TownHall::class, 'users_town_halls', 'users_id', 'townhalls_id' , 'id', 'id');
    }
    public function level() {
        return $this->hasOneThrough(Level::class, UsersTownHall::class, 'users_id', 'id', 'id', 'level_id')
            ->where('users_town_halls.townhalls_id', session('townhall_id'));
    }
    public function menus() {
        return $this->belongsToMany(Permission::class, 'model_has_permissions', 'model_id', 'permission_id' , 'id', 'id')
            ->join('model_has_permissions as mhp', 'mhp.permission_id', 'permissions.id')
            ->where('model_has_permissions.model_type', User::class)
            ->where('permissions.model', Menu::class)
            ;
    }
    public function submenus() {
        return $this->hasManyThrough(Permission::class, ModelHasPermission::class, 'model_id', 'id', 'id' , 'permission_id')
            ->join('menus', 'menus.id', 'permissions.model_id')
            ->where('model_has_permissions.model_type', User::class)
            ->where('permissions.model', Menu::class)
            ->whereColumn('menus.id', '<>', 'menus.pmenus_id');
    }
    public function fsubmenus() {
        return $this->hasManyThrough(Permission::class, ModelHasPermission::class, 'model_id', 'id', 'id' , 'permission_id')
            ->join('menus', 'menus.id', 'permissions.model_id')
            ->where('model_has_permissions.model_type', User::class)
            ->where('model_has_permissions.favorite', 1)
            ->where('permissions.model', Menu::class)
            ->whereColumn('menus.id', '<>', 'menus.pmenus_id');
    }
    public function schedules() {
        return $this->belongsToMany(Schedule::class, UsersSchedule::class, 'users_id', 'schedules_id')
            ->where('schedules.townhalls_id', session('townhall_id'));
    }
    public function shows_permissions() {
        return $this->belongsToMany(Show::class, ShowsUsersPermission::class, 'users_id', 'shows_id');
    }
    public function installations_permissions() {
        return $this->belongsToMany(SportsInstallation::class, SportsInstallationsUsersPermission::class, 'users_id', 'installations_id');
    }

    /**
	 * Overload model save.
	 */
    public function save (array $options = array(), $do_log = true)
    {
        if ($do_log) {
            ModelsLog::doLog(get_class($this).'::save', [
                'original' => $this->getOriginal(),
                'changes' => $this->getDirty(),
            ]);
        }

        parent::save($options); // Calls Default Save
    }

    /**
     * A model may have multiple direct permissions.
     */
    public function permissions(): BelongsToMany
    {
        $relation = $this->morphToMany(
            config('permission.models.permission'),
            'model',
            config('permission.table_names.model_has_permissions'),
            config('permission.column_names.model_morph_key'),
            app(PermissionRegistrar::class)->pivotPermission
        )->withPivot('favorite');

        if (! app(PermissionRegistrar::class)->teams) {
            return $relation;
        }

        return $relation->wherePivot(app(PermissionRegistrar::class)->teamsKey, getPermissionsTeamId());
    }

}
