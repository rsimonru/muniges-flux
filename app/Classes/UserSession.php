<?php

namespace App\Classes;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserSession
{
    public static function session_update($update_login = false)
    {
        $bd_user = User::emtGet(Auth::id());

        if ($update_login) {
            $bd_user->last_login = now();
            $bd_user->save();
        }
    }
}
