<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffEmployee extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'birthday' => 'datetime',
        'contract_start' => 'datetime',
        'contract_end' => 'datetime',
    ];

    /**
     * Get contacts
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['areas', 'state']
    ) {

        $oQuery = static::select('staff_employees.*')
            ->leftJoin('staff_employees_areas as sea', 'sea.employees_id', 'staff_employees.id')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('staff_employees.id', $model_id);
            })
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
                return $query->where('staff_employees.townhalls_id', $filters['townhalls_id']);
            })
            ->when(isset($filters['states_ids']) && ! empty($filters['states_ids']), function ($query) use ($filters) {
                return $query->whereIn('staff_employees.states_id', $filters['states_ids']);
            })
            ->when(isset($filters['areas_id']) && ! empty($filters['areas_id']), function ($query) use ($filters) {
                return $query->where('sea.areas_id', $filters['areas_id']);
            })
            ->when(isset($filters['search']) && ! empty($filters['search']), function ($query) use ($filters) {
                $query->where(function ($query2) use ($filters) {
                    $query2->whereRaw("concat(staff_employees.name, ' ', staff_employees.surname) like '%".$filters['search']."%'")
                        ->orWhere('staff_employees.email', 'like', '%'.$filters['search'].'%');
                });

            });
        $oQuery->groupBy('staff_employees.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function delete($do_log = true)
    {

        StaffEmployeesArea::where('employees_id', $this->id)->delete();
        StaffEmployeesFreeday::where('employees_id', $this->id)->delete();
        parent::delete();

    }

    public function state()
    {
        return $this->hasOne(State::class, 'id', 'states_id');
    }

    public function areas()
    {
        return $this->hasManyThrough(StaffArea::class, StaffEmployeesArea::class, 'employees_id', 'id', 'id', 'areas_id');
    }

    public function staff_employees_areas()
    {
        return $this->hasMany(StaffEmployeesArea::class, 'employees_id', 'id');
    }
}
