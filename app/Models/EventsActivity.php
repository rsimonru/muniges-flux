<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;
use Illuminate\Support\Facades\DB;

class EventsActivity extends Model
{
    use HasFactory;
    use WithExtensions;
    use HasTranslations;

    public $translatable = ['name'];

    /**
     * Get events activities
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

        $oQuery = static::select('events_activities.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('events_activities.id', $model_id);
        })
        ->when(isset($filters['events_id']) && !empty($filters['events_id']), function($query) use ($filters) {
            return $query->where('events_activities.events_id', $filters['events_id']);
        })
        ->when(isset($filters['name']) && !empty($filters['name']), function($query) use ($filters) {
            $query->whereRaw('lower(events_activities.name->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['name']).'%"');
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    /**
     * Get sequential
     *
     * @return int sequential
     *
     */
    public function getSequential() {
        $sequential = 0;
        if (!empty($this->id)) {
            $id = $this->id;
            DB::transaction(function () use (&$sequential, $id) {
                $activity = static::find($id);
                $activity->sequential++;
                $sequential = $activity->sequential;
                $activity->save([],false);
            }, 5);
        }
        return $sequential;
    }

    /**
     * Update registered
     *
     * @return void
     *
     */
    public function updateRegistered($sign) {
        if (!empty($this->id)) {
            $id = $this->id;
            DB::transaction(function () use ($id, $sign) {
                $activity = static::find($id);
                if ($sign>=0) {
                    $activity->registered++;
                }
                else {
                    if ($activity->registered>0) {
                        $activity->registered--;
                    }
                }
                $activity->save([],false);
            }, 5);
        }
    }

    public function event() {
        return $this->hasOne(Event::class, 'id', 'events_id');
    }
    public function events_registrations_activities() {
        return $this->hasMany(EventsRegistrationsActivity::class, 'activities_id', 'id');
    }

}
