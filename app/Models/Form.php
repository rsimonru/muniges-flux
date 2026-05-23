<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Form extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['description', 'observations', 'path'];

    /**
     * Get forms
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['forms_area']
    ) {

        $oQuery = static::select('forms.*')
            ->join('forms_areas as a', 'a.id', 'forms.areas_id')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('forms.id', $model_id);
            });

        $oQuery = static::dlApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    /**
     * Apply filters.
     *
     * @return mixed Query
     */
    public static function dlApplyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['areas_id']) && ! empty($filters['areas_id']), function ($query) use ($filters) {
            return $query->where('forms.areas_id', $filters['areas_id']);
        })
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
                return $query->where('forms.townhalls_id', $filters['townhalls_id']);
            })
            ->when(isset($filters['description']) && ! empty($filters['description']), function ($query) use ($filters) {
                $query->whereRaw('lower(forms.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['description']).'%"');
            });

        return $oQuery;
    }

    public function forms_area()
    {
        return $this->hasOne(FormsArea::class, 'id', 'areas_id');
    }
}
