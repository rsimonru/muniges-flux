<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SportsEventsRegistration extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'birthday' => 'date',
        'is_tutor_passport' => 'boolean',
    ];

    protected $fillable = [
        'events_id',
        'name',
        'surname',
        'vat',
        'tutor_vat',
        'tutor_name',
        'is_tutor_passport',
        'address',
        'town',
        'province',
        'zip',
        'phone',
        'email',
        'birthday',
        'states_id',
        'sequential',
        'size',
        'more_info',
        'observations',
        'internal_note',
    ];

    /**
     * Get sport events registrations
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

        $oQuery = static::select('sports_events_registrations.*')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('sports_events_registrations.id', $model_id);
            });

        $oQuery = static::dlApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public static function dlApplyFilters(
        $oQuery,
        ?array $filters = []
    ) {
        $oQuery->when(isset($filters['registrations_ids']) && ! empty($filters['registrations_ids']), function ($query) use ($filters) {
            return $query->whereIn('sports_events_registrations.id', $filters['registrations_ids']);
        })
            ->when(isset($filters['events_id']) && ! empty($filters['events_id']), function ($query) use ($filters) {
                return $query->where('sports_events_registrations.events_id', $filters['events_id']);
            })
            ->when(isset($filters['search']) && ! empty($filters['search']), function ($query) use ($filters) {
                $query->where(function ($query2) use ($filters) {
                    $query2->whereRaw("concat(sports_events_registrations.name, ' ', sports_events_registrations.surname) like '%".$filters['search']."%'")
                        ->orWhere('sports_events_registrations.vat', 'like', '%'.$filters['search'].'%')
                        ->orWhere('sports_events_registrations.tutor_vat', 'like', '%'.$filters['search'].'%')
                        ->orWhere('sports_events_registrations.tutor_name', 'like', '%'.$filters['search'].'%')
                        ->orWhereHas('sports_events_registrations_activities', function ($query) use ($filters) {
                            return $query->where('sports_events_registrations_activities.group_name', 'like', '%'.$filters['search'].'%');
                        });
                });
            })
            ->when(isset($filters['states_id']) && ! empty($filters['states_id']), function ($query) use ($filters) {
                return $query->where('sports_events_registrations.states_id', $filters['states_id']);
            })
            ->when(isset($filters['activity_states_id']) && ! empty($filters['activity_states_id']), function ($query) use ($filters) {
                return $query->whereHas('sports_events_registrations_activities', function ($query) use ($filters) {
                    return $query->where('sports_events_registrations_activities.states_id', $filters['activity_states_id']);
                });
            })
            ->when(isset($filters['duplicates']) && ! empty($filters['duplicates']), function ($query) use ($filters) {
                return $query->where('sports_events_registrations.name', $filters['duplicates']['name'])
                    ->where('sports_events_registrations.surname', $filters['duplicates']['surname']);
            })
            ->when(isset($filters['activities_ids']) && ! empty($filters['activities_ids']), function ($query) use ($filters) {
                return $query->whereHas('sports_events_registrations_activities', function ($query) use ($filters) {
                    $query->whereIn('sports_events_registrations_activities.activities_id', $filters['activities_ids']);
                });
            })
            ->when(isset($filters['date']) && isset($filters['datetype']) && ! empty($filters['date']) && ! empty($filters['datetype']), function ($query) use ($filters) {
                return $query->whereBetween('sports_events_registrations.'.$filters['datetype'], $filters['date']);
            });

        return $oQuery;
    }

    public function delete($do_log = true)
    {
        SportsEventsRegistrationsActivity::where('registrations_id', $this->id)->delete();
        parent::delete();
    }

    public function state()
    {
        return $this->hasOne(State::class, 'id', 'states_id');
    }

    public function sports_event()
    {
        return $this->hasOne(SportsEvent::class, 'id', 'events_id');
    }

    public function sports_events_registrations_activities()
    {
        return $this->hasMany(SportsEventsRegistrationsActivity::class, 'registrations_id', 'id')
            ->where('sports_events_registrations_activities.states_id', config('states.active'));
    }

    public function sports_events_registrations_activities_all()
    {
        return $this->hasMany(SportsEventsRegistrationsActivity::class, 'registrations_id', 'id');
    }

    public function sports_events_registrations_payments()
    {
        return $this->hasMany(SportsEventsRegistrationsPayment::class, 'registrations_id', 'id');
    }
}
