<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;

class TestController extends Controller
{
    //
    public function test()
    {
        // $user = User::emtGet(1, with: ['permissions', 'permissions_menus']);
        // dd($user->permissions_menus->first());

        // $menu = Menu::find(1);
        // dd($menu->users);

        // $oMenus = Menu::emtGet(0, 10,
        //     [
        //         'menus.order' => 'asc',
        //         'menus.deep' => 'asc',
        //         'menus.description' => 'asc',
        //     ],
        //     [
        //         'iUsers_id' => 1,
        //         'bFavorites' => 0,
        //         'iTownHalls_id' => 1,
        //         'level' => 100,
        //     ], ['submenus']
        // );
        // dd($oMenus);

        $aFilters['bFavorites'] = 0;
        $permissions = User::find(1)->permissions()
            ->where('model', Menu::class)
            ->when($aFilters['bFavorites'] == 1, function ($coll) {
                return $coll->where('pivot.favorite', 1);
            })
            ->get()->keyBy('id')
            ->keys()->all();
        dd($permissions);
    }
}
