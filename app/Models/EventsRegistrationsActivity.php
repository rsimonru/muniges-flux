<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventsRegistrationsActivity extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'registration_date' => 'datetime',
        'withdrawl_date' => 'datetime',
    ];

    /**
     * Get events registrations activities
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

        $oQuery = static::select('events_registrations_activities.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('events_registrations_activities.id', $model_id);
        })
        ->when(isset($filters['registrations_id']) && !empty($filters['registrations_id']), function($query) use ($filters) {
            return $query->where('events_registrations_activities.registrations_id', $filters['registrations_id']);
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function state() {
        return $this->hasOne(State::class, 'id', 'states_id');
    }
    public function events_registration() {
        return $this->hasOne(EventsRegistration::class, 'id', 'registrations_id');
    }
    public function events_activity() {
        return $this->hasOne(EventsActivity::class, 'id', 'activities_id');
    }

}
