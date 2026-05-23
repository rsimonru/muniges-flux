<?php

namespace App\Classes;

use App\Models\Menu;
use App\Models\TownHallsLang;
use App\Models\TownHallsUrl;
use App\Models\User;
use App\Models\UsersTownHall;
use Illuminate\Support\Facades\Auth;

class UserSession
{
    public static function townhall_session_update()
    {
        $url = parse_url(app('request')->fullUrl());
        $townhall_url = TownHallsUrl::where('url', parse_url($url['host']))->get()->first();

        $townhall = $townhall_url->townhall;

        // session(['townhall' => $townhall_url->townhall]);
        session(['townhall_id' => $townhall_url->townhalls_id]);
        session(['townhall_data' => [
            'name' => $townhall->name,
            'short_name' => $townhall->short_name,
            'phone' => $townhall->phone,
            'email' => $townhall->email,
            'web' => $townhall->web,
            'address' => $townhall->address,
            'vat' => $townhall->vat,
        ]]);
        session(['url_type' => $townhall_url->type]);
        $langs = TownHallsLang::emtGet(records_in_page: -1, filters: [
            'townhalls_id' => $townhall_url->townhalls_id,
        ]);
        $langs = array_keys($langs->keyBy('lang')->toArray());
        session(['langs' => array_combine($langs, $langs)]);

        return $townhall;
    }

    public static function user_session_update($update_login = false, $townhall = null)
    {
        $townhall_id = session('townhall_id');
        $utownhall = UsersTownHall::where('users_id', Auth::user()->id)
            ->where('townhalls_id', $townhall_id)
            ->get()->first();
        // session(['user_level_id' => $utownhall->level_id ?? 1]);
        session(['user_level_id' => $utownhall->roles_id ?? 1]);

        $menus = Menu::emtGetUser(Auth::user()->id, 0, $townhall->id ?? 0);
        session(['menus' => $menus]);

        if ($update_login) {
            $bd_user = User::emtGet(Auth::user()->id);
            if (session('url_type') == 'intranet') {
                $bd_user->last_login = now();
                $bd_user->save();
            }
        }
    }
}
