<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Devlab\LaravelLogs\Traits\WithExtensions;

class StaffEmployeesFreeday extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
    ];

    /**
     * Get employees free days
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
        array $with = ['reason','employee']
    ) {

        $oQuery = static::select('staff_employees_freedays.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('staff_employees_freedays.id', $model_id);
        })
        ->when(isset($filters['employees_id']) && !empty($filters['employees_id']), function($query) use ($filters) {
            return $query->where('staff_employees_freedays.employees_id', $filters['employees_id']);
        })
        ->when(isset($filters['reasons_id']) && !empty($filters['reasons_id']), function($query) use ($filters) {
            return $query->where('staff_employees_freedays.reasons_id', $filters['reasons_id']);
        })
        ->when(isset($filters['employees_ids']) && !empty($filters['employees_ids']), function($query) use ($filters) {
            return $query->whereIn('staff_employees_freedays.employees_id', $filters['employees_ids']);
        })
        ->when(isset($filters['reasons_ids']) && !empty($filters['reasons_ids']), function($query) use ($filters) {
            return $query->whereIn('staff_employees_freedays.reasons_id', $filters['reasons_ids']);
        })
        ->when(isset($filters['date']) && !empty($filters['date']), function ($query) use ($filters) {
            return $query->where(function ($query) use ($filters){
                $query->whereBetween('staff_employees_freedays.from_date', $filters['date'])
                    ->orWhereBetween('staff_employees_freedays.to_date', $filters['date']);
            });
        })
        ;
        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function employee() {
        return $this->hasOne(StaffEmployee::class, 'id', 'employees_id');
    }
    public function reason() {
        return $this->hasOne(StaffFreedaysReason::class, 'id', 'reasons_id')
        ->with('color');
    }
}
