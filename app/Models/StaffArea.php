<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffArea extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['description'];

    /**
     * Get staff areas
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['employees']
    ) {

        $oQuery = static::select('staff_areas.*')
            ->selectRaw('count(distinct sea.employees_id) as employees_number')
            ->leftJoin('staff_employees_areas as sea', 'sea.areas_id', 'staff_areas.id')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('staff_areas.id', $model_id);
            })
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
                return $query->where('staff_areas.townhalls_id', $filters['townhalls_id']);
            })
            ->when(isset($filters['description']) && ! empty($filters['description']), function ($query) use ($filters) {
                $query->whereRaw('lower(staff_areas.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['description']).'%"');
            });
        $oQuery->groupBy('staff_areas.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function delete($do_log = true)
    {

        StaffEmployeesArea::where('areas_id', $this->id)->delete();
        parent::delete();

    }

    public function employees()
    {
        return $this->hasManyThrough(StaffEmployee::class, StaffEmployeesArea::class, 'areas_id', 'id', 'id', 'employees_id');
    }

    public function staff_employees_areas()
    {
        return $this->hasMany(StaffEmployeesArea::class, 'areas_id', 'id');
    }
}
