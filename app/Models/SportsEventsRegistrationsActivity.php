<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SportsEventsRegistrationsActivity extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'registration_date' => 'datetime',
        'withdrawl_date' => 'datetime',
    ];

    /**
     * Get sport events registrations activities
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['sports_events_registration', 'sports_events_activity', 'state']
    ) {

        $oQuery = static::select('sports_events_registrations_activities.*')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('sports_events_registrations_activities.id', $model_id);
            })
            ->when(isset($filters['registrations_id']) && ! empty($filters['registrations_id']), function ($query) use ($filters) {
                return $query->where('sports_events_registrations_activities.registrations_id', $filters['registrations_id']);
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function save($options = [], $do_log = true)
    {
        $activity = SportsEventsActivity::find($this->activities_id);
        $changed_active = ($this->isDirty('states_id') && $this->states_id == config('states.active'));
        $changed_inactive = ($this->isDirty('states_id') && $this->states_id == config('states.inactive'));
        if ($changed_active) {
            if ($activity->registered >= $activity->quota) {
                return -1; // Out of quota
            }
            if (empty($this->id)) {
                $this->sequential = $activity->getSequential();
            }
            $activity->updateRegistered(1);
        }
        if ($changed_inactive) {
            $activity->updateRegistered(-1);
        }
        parent::save();

        return $this->id;
    }

    public function delete($do_log = true)
    {
        $activity = SportsEventsActivity::find($this->activities_id);
        $activity->updateRegistered(-1);
        parent::delete();
    }

    public function state()
    {
        return $this->hasOne(State::class, 'id', 'states_id');
    }

    public function sports_events_registration()
    {
        return $this->hasOne(SportsEventsRegistration::class, 'id', 'registrations_id');
    }

    public function sports_events_activity()
    {
        return $this->hasOne(SportsEventsActivity::class, 'id', 'activities_id');
    }
}
