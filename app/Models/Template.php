<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class Template extends Model
{
    use HasFactory;
    use WithExtensions;
    use HasTranslations;

    public $translatable = ['content'];

    public $attributes = [
        'ttypes_id' => null,
        'tsections_id' => null,
        'tobjects_id' => null,
        'content' => null,
        'townhalls_id' => null,
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get schedules
     *
     * @param int $model_id
     * @param int $records_in_page
     * @param array $sort (attribute => 'asc'/'desc')
     * @param array $filters
     * @return mixed Collection
     *
     */
    public static function emtGet(
        int $model_id=0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = []
    ) {

        $oQuery = static::select('templates.*')
        ->join('templates_sections as s', 's.id', 'templates.tsections_id')
        ->join('templates_objects as o', 'o.id', 'templates.tobjects_id')
        ->join('templates_types as t', 't.id', 'templates.ttypes_id')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('templates.id', $model_id);
        });

        $oQuery = static::dlApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }
    /**
     * Apply filters.
     *
     * @param $oQuery
     * @param array $filters
     * @return mixed Query
     *
     */
    public static function dlApplyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('templates.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['tsections_id']) && !empty($filters['tsections_id']), function($query) use ($filters) {
            return $query->where('templates.tsections_id', $filters['tsections_id']);
        })
        ->when(isset($filters['tobjects_id']) && !empty($filters['tobjects_id']), function($query) use ($filters) {
            return $query->where('templates.tobjects_id', $filters['tobjects_id']);
        })
        ->when(isset($filters['ttypes_id']) && !empty($filters['ttypes_id']), function($query) use ($filters) {
            return $query->where('templates.ttypes_id', $filters['ttypes_id']);
        })
        ->when(isset($filters['description']) && !empty($filters['description']), function($query) use ($filters) {
            $query->where('templates.description', 'like', '%'.$filters['description'].'%');
        });

        return $oQuery;
    }

    public function section() {
        return $this->hasOne(TemplatesSection::class, 'id', 'tsections_id');
    }
    public function object() {
        return $this->hasOne(TemplatesObject::class, 'id', 'tobjects_id');
    }
    public function type() {
        return $this->hasOne(TemplatesType::class, 'id', 'ttypes_id');
    }

}
