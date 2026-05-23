<?php

namespace App\Http\Middleware;

use App\Classes\UserSession;
use App\Models\TownHallsUrl;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            $url = parse_url($request->getUri());

            // if (config('app.env') == 'local') {
            //     $townhall_url = TownHallsUrl::where('id', 10)->get()->first();
            // } else {
            //     $townhall_url = TownHallsUrl::where('url', $url['host'])->get()->first();
            // }
            $townhall_url = TownHallsUrl::where('url', $url['host'])->get()->first();
            if ($townhall_url) {
                if ($townhall_url->type == 'sac' || $townhall_url->type == 'app') {
                    $user = User::emtGet(
                        records_in_page: -1,
                        filters: [
                            'townhalls_id' => $townhall_url->townhalls_id,
                            'level_id' => config('constants.levels.citizen'),
                            'active' => 1,
                        ]
                    );
                    if (length($user) > 0) {
                        Auth::login($user->first());

                        return $request->path();
                    } else {
                        return route('login');
                    }
                } else {
                    // dd('entro');
                    UserSession::townhall_session_update();

                    return route('login');
                }
            } else {
                return 'https://www.muniges.es';
            }
        }

        return null;
    }
}
