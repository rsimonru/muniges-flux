<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogsMethod extends Model
{
    use HasFactory;
    use WithExtensions;

    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = []
    ) {

        $oQuery = static::select('logs_methods.*')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('logs_methods.id', $model_id);
            })
            ->when(isset($filters['name']) && ! empty($filters['name']), function ($query) use ($filters) {
                return $query->where('logs_methods.name', $filters['name']);
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }
}
