<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['description'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get menus
     *
     * @param  int  $user_id
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @param  int  $bFavorites
     * @param  int  $iTownHalls_id
     * @param  int  $level
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = []
    ) {

        $oQuery = Menu::select('menus.*')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('menus.id', $model_id);
            })
            ->when(isset($filters['level']) && $filters['level'] > 0, function ($query) use ($filters) {
                return $query->where('menus.level', '<=', $filters['level']);
            })
        // ->when(isset($filters['description']) && !empty($filters['description']), function($query) use ($filters) {
        //     $query->whereRaw('lower(menus.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['description']).'%"');
        // })
            ->when(isset($filters['iUsers_id']) && $filters['iUsers_id'] > 0, function ($query) use ($filters) {
                $user = User::find($filters['iUsers_id']);

                $menus_ids = $user->menus
                    ->when($filters['bFavorites'] == 1, function ($coll) {
                        return $coll->where('pivot.favorite', 1);
                    })
                    ->when(isset($filters['level']) && $filters['level'] > 0, function ($coll) use ($filters) {
                        return $coll->where('level', '<=', $filters['level']);
                    })
                    ->keyBy('model_id')->keys()->all();

                $query->whereIn('menus.id', $menus_ids);
                $query2 = Menu::select('menus.*')
                    ->whereIn('menus.id', function ($query) use ($menus_ids) {
                        $query->select('menus.pmenus_id')
                            ->from('menus')
                            ->whereIn('menus.id', $menus_ids);
                    })
                    ->union($query);

                return $query2;
            });

        if (! isset($filters['iUsers_id'])) {
            foreach ($sort as $key => $value) {
                $oQuery->orderBy($key, $value);
            }
        }
        // $oQuery->dd();
        $records = static::getModelData($oQuery, $model_id, $records_in_page, $with);
        if (isset($filters['iUsers_id']) && $filters['iUsers_id'] > 0) {
            $records->sortBy($sort);
        }

        return $records;
    }

    /**
     * Get user menus
     *
     * @param  int  $user_id
     * @return mixed Collection
     */
    public static function emtGetUser(
        int $iUsers_id = 0,
        int $bFavorites = 0,
        int $iTownHalls_id = 0,
        int $level = 0
    ) {

        $oMenus = static::emtGet(0, -1,
            [
                'menus.order' => 'asc',
                'menus.deep' => 'asc',
                'menus.description' => 'asc',
            ],
            [
                'iUsers_id' => $iUsers_id,
                'bFavorites' => $bFavorites,
                'level' => $level,
            ],
            ['permission']
        );
        $oTownHall = (! empty($iTownHalls_id)) ? TownHall::find($iTownHalls_id) : null;

        $aMenus = [];
        if ($oMenus->count() > 0) {
            foreach ($oMenus as $oMenu) {
                if ($oTownHall && $oMenu->type == 'url' && $oMenu->route == '[web-sede]') {
                    $oMenu->route = $oTownHall->virtual_office;
                }
                if ($oMenu->deep == 1) {
                    if (isset($aMenus[$oMenu->id]['submenu'])) {
                        $aMenus[$oMenu->id] = array_merge($oMenu->attributes, $aMenus[$oMenu->id]);
                    } else {
                        $aMenus[$oMenu->id] = $oMenu->attributes;
                        $aMenus[$oMenu->id]['submenu'] = [];
                        $aMenus[$oMenu->id]['permission'] = $oMenu->permission;
                    }
                }
                if ($oMenu->deep == 2) {
                    if (isset($aMenus[$oMenu->pmenus_id]['submenu'][$oMenu->id])) {
                        $aMenus[$oMenu->pmenus_id]['submenu'][$oMenu->id] = array_merge($oMenu->attributes, $aMenus[$oMenu->pmenus_id]);
                    } else {
                        $aMenus[$oMenu->pmenus_id]['submenu'][$oMenu->id] = $oMenu->attributes;
                        $aMenus[$oMenu->pmenus_id]['submenu'][$oMenu->id]['submenu'] = [];
                        $aMenus[$oMenu->pmenus_id]['submenu'][$oMenu->id]['permission'] = $oMenu->permission;
                    }
                }
                // if($oMenu->deep==3){
                //     $aMenus[$oMenu->pmenus_id]['submenu'][$oMenu->pmenus_id]['submenu'][$oMenu->id] = $oMenu->attributes;
                //     $aMenus[$oMenu->pmenus_id]['submenu'][$oMenu->pmenus_id]['submenu'][$oMenu->id]['submenu'] = array();
                // }
            }
        }
        $aMenusResult = [];
        foreach ($aMenus as $index => $menu) {
            $aMenusResult[$index] = $menu;
            $aMenusResult[$index]['submenu'] = collect($menu['submenu'])->sortBy('order');
        }

        return collect($aMenusResult)->sortBy('order');
    }

    public function submenus()
    {
        return $this->hasMany(Menu::class, 'pmenus_id', 'id')->whereColumn('menus.pmenus_id', '<>', 'menus.id');
    }

    public function permission()
    {
        return $this->hasOne(Permission::class, 'model_id', 'id')
            ->where('permissions.model', Menu::class);
    }

    public static function getUsers($ids, $favorites = false)
    {
        $users = User::select('users.*', 'menus.id as menu_id')
            ->join('model_has_permissions', 'model_has_permissions.model_id', 'users.id')
            ->join('permissions', 'permissions.id', 'model_has_permissions.permission_id')
            ->join('menus', 'menus.id', 'permissions.model_id')
            ->join('users_town_halls', 'users_town_halls.users_id', 'users.id')
            ->where('permissions.model', Menu::class)
            ->where('users_town_halls.townhalls_id', session('townhall_id'))
            ->when($favorites, function ($query) {
                $query->where('model_has_permissions.favorite', 1);
            })
            ->whereIn('menus.id', $ids)
            ->get();

        $grouped = $users->groupBy('menu_id');

        return $grouped;
    }

    public static function getGroups($ids, $favorites = false)
    {
        $groups = Role::select('roles.*', 'menus.id as menu_id')
            ->join('role_has_permissions', 'role_has_permissions.role_id', 'roles.id')
            ->join('permissions', 'permissions.id', 'role_has_permissions.permission_id')
            ->join('menus', 'menus.id', 'permissions.model_id')
            ->where('permissions.model', Menu::class)
            ->where('roles.townhalls_id', session('townhall_id'))
            ->when($favorites, function ($query) {
                $query->where('role_has_permissions.favorite', 1);
            })
            ->whereIn('menus.id', $ids)
            ->get();

        $grouped = $groups->groupBy('menu_id');

        return $grouped;
    }

    // public function users() {
    //     return $this->belongsToMany(User::class, ModelHasPermission::class, 'permission_id1', 'model_id', 'id', 'id')
    //         ->join('permissions', 'model_has_permissions.permission_id', 'permissions.id')
    //         ->where('permissions.model', Menu::class)
    //         ->where('model_has_permissions.model_type', User::class);
    // }
    // public function users_favorites() {
    //     return $this->belongsToMany(User::class, ModelHasPermission::class, 'permission_id', 'model_id')
    //         ->join('permissions', 'model_has_permissions.model_id', 'permissions.id')
    //         ->where('model_has_permissions.model_type', User::class);
    // }
    // public function groups() {
    //     return $this->belongsToMany(Role::class, RoleHasPermission::class, 'role_id', 'permission_id')
    //         ->join('permissions', 'role_has_permissions.permission_id', 'permissions.id');
    // }
    // public function groups_favorites() {
    //     return $this->belongsToMany(Role::class, RoleHasPermission::class, 'role_id', 'permission_id')
    //         ->join('permissions', 'role_has_permissions.permission_id', 'permissions.id')
    //         ->where('role_has_permissions.favorite', 1);
    // }
    // public function users() {
    //     return $this->belongsToMany(User::class, UsersMenu::class, 'menus_id', 'users_id')
    //         ->whereExists(function ($query) {
    //             $query->selectRaw(1)
    //                 ->from('users_town_halls as uth')
    //                 ->where('uth.townhalls_id', session('townhall_id'))
    //                 ->whereColumn('uth.users_id', 'users.id');
    //         });
    // }
    // public function users_favorites() {
    //     return $this->belongsToMany(User::class, UsersMenu::class, 'menus_id', 'users_id')
    //         ->where('users_menus.favorite', 1)
    //         ->whereExists(function ($query) {
    //             $query->selectRaw(1)
    //                 ->from('users_town_halls as uth')
    //                 ->where('uth.townhalls_id', session('townhall_id'))
    //                 ->whereColumn('uth.users_id', 'users.id');
    //         });
    // }
    // public function groups() {
    //     return $this->belongsToMany(Group::class, GroupsMenu::class, 'menus_id', 'groups_id')
    //         ->where('groups.townhalls_id', session('townhall_id'));
    // }
    // public function groups_favorites() {
    //     return $this->belongsToMany(Group::class, GroupsMenu::class, 'menus_id', 'groups_id')
    //         ->where('groups_menus.favorite', 1)
    //         ->where('groups.townhalls_id', session('townhall_id'));
    // }
}
