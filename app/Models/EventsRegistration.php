<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventsRegistration extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'birthday' => 'datetime',
    ];

    /**
     * Get events registrations
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = []
    ) {

        $oQuery = static::select('events_registrations.*')
            ->selectRaw('group_concat(a.name->>"$.'.app()->getLocale()."\" separator ', ') as activities, group_concat(ra.group_name separator ', ') as `groups`")
            ->leftJoin('events_registrations_activities as ra', 'ra.registrations_id', 'events_registrations.id')
            ->leftJoin('events_activities as a', 'a.id', 'ra.activities_id')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('events_registrations.id', $model_id);
            })
            ->when(isset($filters['registrations_ids']) && ! empty($filters['registrations_ids']), function ($query) use ($filters) {
                return $query->whereIn('events_registrations.id', $filters['registrations_ids']);
            })
            ->when(isset($filters['events_id']) && ! empty($filters['events_id']), function ($query) use ($filters) {
                return $query->where('events_registrations.events_id', $filters['events_id']);
            })
            ->when(isset($filters['search']) && ! empty($filters['search']), function ($query) use ($filters) {
                $query->where(function ($query2) use ($filters) {
                    $query2->whereRaw("concat(events_registrations.name, ' ', events_registrations.surname) like '%".$filters['search']."%'")
                        ->orWhere('events_registrations.vat', 'like', '%'.$filters['search'].'%')
                        ->orWhere('events_registrations.tutor_vat', 'like', '%'.$filters['search'].'%')
                        ->orWhere('events_registrations.tutor_name', 'like', '%'.$filters['search'].'%');
                });
            })
            ->when(isset($filters['states_id']) && ! empty($filters['states_id']), function ($query) use ($filters) {
                return $query->where('events_registrations.states_id', $filters['states_id']);
            })
            ->when(isset($filters['duplicates']) && ! empty($filters['duplicates']), function ($query) use ($filters) {
                return $query->where('events_registrations.name', $filters['duplicates']['name'])
                    ->where('events_registrations.surname', $filters['duplicates']['surname']);
            })
            ->when(isset($filters['activities_ids']) && ! empty($filters['activities_ids']), function ($query) use ($filters) {
                return $query->whereIn('ra.activities_id', $filters['activities_ids']);
            })
            ->when(isset($filters['date']) && isset($filters['datetype']) && ! empty($filters['date']) && ! empty($filters['datetype']), function ($query) use ($filters) {
                return $query->whereBetween('events_registrations.'.$filters['datetype'], $filters['date']);
            });
        $oQuery->groupBy('events_registrations.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function delete($do_log = true)
    {
        EventsRegistrationsActivity::where('registrations_id', $this->id)->delete();
        parent::delete();
    }

    public function state()
    {
        return $this->hasOne(State::class, 'id', 'states_id');
    }

    public function event()
    {
        return $this->hasOne(Event::class, 'id', 'events_id');
    }

    public function events_registrations_activities()
    {
        return $this->hasMany(EventsRegistrationsActivity::class, 'registrations_id', 'id')
            ->where('events_registrations_activities.states_id', config('states.active'))
            ->with('events_activity');
    }

    public function events_registrations_activities_all()
    {
        return $this->hasMany(EventsRegistrationsActivity::class, 'registrations_id', 'id')
            ->with('events_activity');
    }

    public function events_registrations_payments()
    {
        return $this->hasMany(EventsRegistrationsPayment::class, 'registrations_id', 'id')->with('events_payment', 'treasury_billing_code');
    }
}
