<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServicesEventsNote extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * Get services events notes
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
        array $with = ['services_event', 'state', 'user']
    ) {

        $oQuery = static::select('services_events_notes.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('services_events_notes.id', $model_id);
        })
        ->when(isset($filters['events_id']) && !empty($filters['events_id']), function($query) use ($filters) {
            return $query->where('services_events_notes.events_id', $filters['events_id']);
        })
        ->when(isset($filters['state']) && !empty($filters['state']), function($query) use ($filters) {
            return $query->where('services_events_notes.state', $filters['state']);
        })
        ->when(isset($filters['observations']) && !empty($filters['observations']), function($query) use ($filters) {
            $query->where('services_events_notes.observations', 'like', '%'.$filters['observations'].'%');
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function services_event() {
        return $this->hasOne(ServicesEvent::class, 'id', 'events_id')->with('services_category');
    }
    public function state() {
        return $this->hasOne(State::class, 'id', 'states_id');
    }
    public function user() {
        return $this->hasOne(User::class, 'id', 'created_user');
    }
    public function assigned_user() {
        return $this->hasOne(User::class, 'id', 'assigned_user_id');
    }

}
