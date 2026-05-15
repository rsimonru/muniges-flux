<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Log;

class TownHallScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $table_name = $model->getTable();

        if (!empty(session('townhall_id'))) {
            if ($table_name == 'town_halls') {
                $builder->where($table_name.'.id', session('townhall_id'));
            } elseif ($table_name == 'users') {
                $builder->has('townhall');
            } else {
                $builder->where($table_name.'.townhalls_id', session('townhall_id'));
            }
        }
    }
}
