<?php

namespace Database\Seeders;

use App\Models\TownHall;
use App\Models\TownHallsUrl;
use App\Models\User;
use App\Models\UsersMenu;
use App\Models\UsersTownHall;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialUserDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'name' => 'superadmin', 'email' => 'info@muniges.es', 'password' => Hash::make('$muniges2015'), 'active' => 1,],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'name' => 'Ciudadano', 'email' => 'ciudadano@muniges.es', 'password' => '_SinClave_', 'active' => 1,],
        ];
        User::upsert($users, ['id'], ['name', 'email', 'password', 'active', 'created_at', 'updated_at']);

        $townhalls = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'name' => json_encode(['es' => 'Ayto Muniges', 'en' => 'Ayto Muniges']), 'short_name' => json_encode(['es' => 'Ayto Muniges', 'en' => 'Ayto Muniges']),
                 'from_email' => 'info@muniges.es', 'web' => 'www.muniges.es', 'url_prefix' => 'municipio', 'tpvpc_config_secret' => '*', 'app_secret' => '*', ],
        ];
        TownHall::upsert($townhalls, ['id'], ['name', 'short_name', 'from_email', 'web', 'url_prefix', 'tpvpc_config_secret', 'app_secret', 'created_at', 'updated_at']);

        $townhalls_urls = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'townhalls_id' => 1, 'type' => 'intranet', 'url' => 'municipio-int.muniges.es', 'order' => 1,],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'townhalls_id' => 1, 'type' => 'sac', 'url' => 'municipio.muniges.es', 'order' => 1,],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'townhalls_id' => 1, 'type' => 'app', 'url' => 'municipio-app.muniges.es', 'order' => 1,],
        ];
        TownHallsUrl::upsert($townhalls_urls, ['id'], ['townhalls_id', 'type', 'url', 'order', 'created_at', 'updated_at']);

        $users_menus = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'users_id' => 1, 'menus_id' => 14, 'favorite' => 1,],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'users_id' => 1, 'menus_id' => 16, 'favorite' => 1,],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'users_id' => 1, 'menus_id' => 65, 'favorite' => 1,],
        ];
        UsersMenu::upsert($users_menus, ['id'], ['users_id', 'menus_id', 'favorite', 'created_at', 'updated_at']);

        $users_townhalls = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'users_id' => 1, 'townhalls_id' => 1, 'level_id' => 6,],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'users_id' => 2, 'townhalls_id' => 1, 'level_id' => 1,],
        ];
        UsersTownHall::upsert($users_townhalls, ['id'], ['users_id', 'townhalls_id', 'level_id', 'created_at', 'updated_at']);
    }
}
