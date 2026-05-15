<?php

namespace App\Listeners;

use App\Classes\UserSession;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
        $townhall = UserSession::townhall_session_update();
        UserSession::user_session_update(true, $townhall);
    }
}
