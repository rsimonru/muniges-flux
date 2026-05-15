<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), "name" => "BOT_DEP_INSCRIP_MODIF",        "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Deportes - Inscripciones - Modificar", "en"=> "Sports - Inscriptions - Modify"]), ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), "name" => "BOT_TRIB_ALTERRENO_BORRAR",    "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Tributos - Autoliquidación Plusvalia - Eliminar", "en"=> "Taxes - Self settlement - Delete"]), ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), "name" => "BOT_TES_CODCOBRO_DESBLOQ",     "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Tesorería - Código pago - Desbloquear", "en"=> "Treasury - Payment code - Unlock"]), ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), "name" => "BOT_TES_BANCO_BORRARMOV",      "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Tesorería - Bancos - Borrar movimientos", "en"=> "Treasury - Banks - Delete movements"]), ],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), "name" => "BOT_IMPRESOS_GUARDAR",         "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Impresos - Nuevo/modificar/eliminar", "en"=> "Forms - New/edit/delete"]), ],
            ["id" => 6, "created_at" => now(), "updated_at" => now(), "name" => "BOT_TES_CODCOBRO_BORRAR",      "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Tesorería - Código pago - Eliminar", "en"=> "Treasury - Payment code - Delete"]), ],
            ["id" => 7, "created_at" => now(), "updated_at" => now(), "name" => "BOT_SERV_ASIGNAR_INCID",       "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Servicios - Incidencias - Asignar", "en"=> "Services - Issues - Assign"]), ],
            ["id" => 8, "created_at" => now(), "updated_at" => now(), "name" => "BOT_NOTIFICACIONES_MODIFICAR", "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Notificaciones - Modificar", "en"=> "Notifications - Modify"]), ],
        ];

        $menus = Menu::all();
        foreach ($menus as $menu) {
            $records[] = [
                'id' => 1000+$menu->id,
                'created_at' => $menu->created_at,
                'updated_at' => $menu->updated_at,
                'name' => 'Menu '. $menu->description . ' ' .$menu->id,
                'guard_name' => 'web',
                'class' => 'model',
                'model' => Menu::class,
                'model_id' => $menu->id,
                'data' => null,
                'level' => $menu->level,
                'description' => json_encode($menu->getTranslations()['description']),
            ];
        }

        $result = Permission::upsert($records, ['id', 'name', 'guard_name'], ['description', 'class', 'model', 'model_id', 'data', 'level', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
