<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Devlab\LaravelLogs\Traits\WithExtensions;
use App\Traits\HasTranslations;

class Form extends Model
{
    use HasFactory;
    use WithExtensions;
    use HasTranslations;

    public $translatable = ['description', 'observations', 'path'];

    /**
     * Get forms
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
        array $with = ['forms_area']
    ) {

        $oQuery = static::select('forms.*')
        ->join('forms_areas as a', 'a.id', 'forms.areas_id')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('forms.id', $model_id);
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

        $oQuery->when(isset($filters['areas_id']) && !empty($filters['areas_id']), function($query) use ($filters) {
            return $query->where('forms.areas_id', $filters['areas_id']);
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('forms.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['description']) && !empty($filters['description']), function($query) use ($filters) {
            $query->whereRaw('lower(forms.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['description']).'%"');
        });

        return $oQuery;
    }

    public function forms_area() {
        return $this->hasOne(FormsArea::class, 'id', 'areas_id');
    }
}
