<?php

namespace App\Listeners;

use App\Classes\UserSession;
use Illuminate\Auth\Events\Login;

class UserAuthenticated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(Login $event)
    {
        //
        $townhall = UserSession::townhall_session_update();
        UserSession::user_session_update(true, $townhall);
    }
}
