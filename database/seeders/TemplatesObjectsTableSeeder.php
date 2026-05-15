<?php

namespace Database\Seeders;

use App\Models\TemplatesObject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TemplatesObjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => 'Pago', ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => 'Notificación', ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'description' => 'Decreto', ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'description' => 'Inscripción', ],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), 'description' => 'Ficha terceros', ],
            ["id" => 6, "created_at" => now(), "updated_at" => now(), 'description' => 'Reserva', ],
            ["id" => 7, "created_at" => now(), "updated_at" => now(), 'description' => 'Listado agendas', ],
            ["id" => 8, "created_at" => now(), "updated_at" => now(), 'description' => 'Solicitud bonificación', ],
            ["id" => 9, "created_at" => now(), "updated_at" => now(), 'description' => 'Genérico', ],
            ["id" => 10, "created_at" => now(), "updated_at" => now(), 'description' => 'Dorsal', ],
            ["id" => 11, "created_at" => now(), "updated_at" => now(), 'description' => 'Informe entradas', ],
        ];

        TemplatesObject::upsert($records, ['id'], ['description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
