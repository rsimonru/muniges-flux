<?php

namespace App\Exports;

use App\Classes\ExcelExport;
use App\Models\Role;

class GroupsExport extends ExcelExport
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
            ['title' => 'Tipo', 'type' => 'string', 'width' => 20],
            ['title' => 'Componentes', 'type' => 'integer', 'width' => 15],
            ['title' => 'Creado', 'type' => 'DD/MM/YYYY HH:MM', 'width' => 20],
            ['title' => 'Modificado', 'type' => 'DD/MM/YYYY HH:MM', 'width' => 20],
        ];

        return $headers;
    }

    public function map($record): array
    {
        $records = [
            $record->id,
            $record->description,
            $record->level ? $record->level->name : '',
            $record->users_count,
            $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : '',
            $record->created_at ? $record->created_at->format('Y-m-d H:i:s') : '',
        ];

        return $records;
    }

    public function setQuery()
    {
        $this->query = Role::select('roles.*')
            ->where('roles.townhalls_id', session('townhall_id', 0))
            ->withCount('users')
            ->with('level');
        $this->query = Role::dlApplyFilters($this->query, $this->filters);
    }
}
