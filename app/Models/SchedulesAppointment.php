<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchedulesAppointment extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
    ];

    /**
     * Get schedules appointments
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['schedule']
    ) {

        $oQuery = static::select('schedules_appointments.*')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('schedules_appointments.id', $model_id);
            })
            ->when(isset($filters['schedules_id']) && ! empty($filters['schedules_id']), function ($query) use ($filters) {
                return $query->where('schedules_appointments.schedules_id', $filters['schedules_id']);
            })
            ->when(isset($filters['schedules_ids']) && ! empty($filters['schedules_ids']), function ($query) use ($filters) {
                return $query->whereIn('schedules_appointments.schedules_id', $filters['schedules_ids']);
            })
            ->when(isset($filters['date']) && ! empty($filters['date']), function ($query) use ($filters) {
                return $query->where(function ($query) use ($filters) {
                    $query->whereBetween('schedules_appointments.from_date', $filters['date'])
                        ->orWhereBetween('schedules_appointments.to_date', $filters['date']);
                });
            })
            ->when(isset($filters['description']) && ! empty($filters['description']), function ($query) use ($filters) {
                $query->where('schedules_appointments.description', 'like', '%'.$filters['description'].'%');
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class, 'id', 'schedules_id')->with('color');
    }
}
