<?php

namespace App\Exports;

use App\Classes\ExcelExport;
use App\Models\User;

class UsersExport extends ExcelExport
{
    public function __construct($filters)
    {
        // dd($filters);
        $this->jobLimit = 2000;
        parent::__construct($filters);
    }

    public function headings(): array
    {

        $headers = [
            ['title' => 'ID', 'type' => 'integer', 'width' => 15],
            ['title' => 'Nombre', 'type' => 'string', 'width' => 30],
            ['title' => 'Email', 'type' => 'string', 'width' => 40],
            ['title' => 'Grupos', 'type' => 'string', 'width' => 40],
            ['title' => 'Estado', 'type' => 'string', 'width' => 25],
            ['title' => 'Ult. conexión', 'type' => 'DD/MM/YYYY HH:MM', 'width' => 20],
            ['title' => 'Creado', 'type' => 'DD/MM/YYYY HH:MM', 'width' => 20],
            ['title' => 'Modificado', 'type' => 'DD/MM/YYYY HH:MM', 'width' => 20],
        ];

        return $headers;
    }

    public function map($record): array
    {
        $roles = $record->roles;
        $rol = '';
        $rolesArray = $roles->toArray();
        $lastKey = array_key_last($rolesArray);
        foreach ($rolesArray as $key => $user_rol) {
            $rol .= $user_rol['description'];
            if ($key !== $lastKey) {
                $rol .= ', ';
            }
        }
        $records = [
            $record->id,
            $record->name,
            $record->email,
            $rol,
            $record->active ? 'Activo' : 'Desactivado',
            $record->last_login ? $record->last_login->format('Y-m-d H:i:s') : '',
            $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : '',
            $record->created_at ? $record->created_at->format('Y-m-d H:i:s') : '',
        ];

        return $records;
    }

    public function setQuery()
    {
        $this->query = User::select('users.*')
            ->join('users_town_halls as uth', 'users.id', 'uth.users_id')
            ->join('levels as l', 'l.id', 'uth.level_id')
            ->where('uth.townhalls_id', session('townhall_id', 0))
            ->with('roles');
        $this->query = User::dlApplyFilters($this->query, $this->filters);
    }
}
