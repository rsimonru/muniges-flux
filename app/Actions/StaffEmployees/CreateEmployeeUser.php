<?php

namespace App\Actions\StaffEmployees;

use App\Models\StaffEmployee;
use App\Models\User;
use App\Models\UsersMenu;
use App\Models\UsersTownHall;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class CreateEmployeeUser
{

    public static function handle(StaffEmployee $employee)
    {
        if ($employee->email) {
            $user = User::emtGet(
                iRecordsInPage: -1,
                aFilters: [
                    'email' => $employee->email,
                    'townhalls_id' => $employee->townhalls_id
                ]
            )->first();
            // dd($user);

            if (empty($user)) {
                $user = new User();
                $user->name = $employee->name . ' ' . $employee->surname;
                $user->email = $employee->email;
                $user->password = Hash::make(time());
                $user->save();

                $utownhall = new UsersTownHall();
                $utownhall->users_id = $user->id;
                $utownhall->roles_id = config('constants.user_roles.employee');
                $utownhall->townhalls_id = $employee->townhalls_id;
                $utownhall->save();

                $user_menu = new UsersMenu();
                $user_menu->users_id = $user->id;
                $user_menu->menus_id = config('constants.menus.employee_portal');
                $user_menu->favorite = 1;
                $user_menu->save();

                $status = static::broker()->sendResetLink(
                    ['email' => $user->email]
                );
            }
        }
    }

    protected static function broker(): PasswordBroker
    {
        return Password::broker(config('fortify.passwords'));
    }
}
