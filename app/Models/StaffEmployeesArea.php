<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Devlab\LaravelLogs\Traits\WithExtensions;

class StaffEmployeesArea extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'areas_id',
        'employees_id',
    ];

    /**
     * Get employees areas
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

        $oQuery = static::select('staff_employees_areas.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('staff_employees_areas.id', $model_id);
        })
        ->when(isset($filters['areas_id']) && !empty($filters['areas_id']), function($query) use ($filters) {
            return $query->where('staff_employees_areas.areas_id', $filters['areas_id']);
        })
        ->when(isset($filters['employees_id']) && !empty($filters['employees_id']), function($query) use ($filters) {
            return $query->where('staff_employees_areas.employees_id', $filters['employees_id']);
        })
        ;
        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //dd($oQuery->toSql());
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    /**
	 * Overload model save.
	 */
    public function save (array $options = array(), $do_log = true)
    {
        $employeesareas = StaffEmployeesArea::where('employees_id',$this->employees_id)->where('areas_id',$this->areas_id)->get();
        if (length($employeesareas)==0) {
            parent::save($options); // Calls Default Save
        }
    }

}
