<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShowsTicket extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'purchase_date' => 'datetime',
        'liberation_date' => 'datetime',
        'date' => 'datetime',
        'used_at' => 'datetime',
        'val_data' => 'json',
    ];

    /**
     * Get shows tickets
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['shows_events_session', 'shows_tickets_type', 'state']
    ) {

        $oQuery = static::select('shows_tickets.*')
            ->join('shows_tickets_types as t', 't.id', 'shows_tickets.types_id')
            ->join('shows_events_sessions as s', 's.id', 'shows_tickets.sessions_id')
            ->join('shows_events as e', 'e.id', 's.events_id')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('shows_tickets.id', $model_id);
            })
            ->when(isset($filters['tickets_ids']) && ! empty($filters['tickets_ids']), function ($query) use ($filters) {
                return $query->whereIn('shows_tickets.id', $filters['tickets_ids']);
            })
            ->when(isset($filters['sessions_id']) && ! empty($filters['sessions_id']), function ($query) use ($filters) {
                return $query->where('shows_tickets.sessions_id', $filters['sessions_id']);
            })
            ->when(isset($filters['events_id']) && ! empty($filters['events_id']), function ($query) use ($filters) {
                return $query->where('e.id', $filters['events_id']);
            })
            ->when(isset($filters['shows_id']) && ! empty($filters['shows_id']), function ($query) use ($filters) {
                return $query->where('e.shows_id', $filters['shows_id']);
            })
            ->when(isset($filters['lock_id']) && ! empty($filters['lock_id']), function ($query) use ($filters) {
                return $query->where('shows_tickets.lock_id', $filters['lock_id']);
            })
            ->when(isset($filters['states_id']) && ! empty($filters['states_id']), function ($query) use ($filters) {
                return $query->where('shows_tickets.states_id', $filters['states_id']);
            })
            ->when(isset($filters['states_ids']) && ! empty($filters['states_ids']), function ($query) use ($filters) {
                return $query->whereIn('shows_tickets.states_id', $filters['states_ids']);
            })
            ->when(isset($filters['types_id']) && ! empty($filters['types_id']), function ($query) use ($filters) {
                return $query->where('shows_tickets.types_id', $filters['types_id']);
            })
            ->when(isset($filters['types_ids']) && ! empty($filters['types_ids']), function ($query) use ($filters) {
                return $query->whereIn('shows_tickets.types_id', $filters['types_ids']);
            })
            ->when(isset($filters['name']) && ! empty($filters['name']), function ($query) use ($filters) {
                $query->whereRaw('lower(shows_tickets.name) COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['name']).'%"');
            })
            ->when(isset($filters['email']) && ! empty($filters['email']), function ($query) use ($filters) {
                $query->where('shows_tickets.email', 'like', '%'.$filters['email'].'%');
            })
            ->when(isset($filters['phone']) && ! empty($filters['phone']), function ($query) use ($filters) {
                $query->where('shows_tickets.phone', 'like', '%'.$filters['phone'].'%');
            })
            ->when(isset($filters['search']) && ! empty($filters['search']), function ($query) use ($filters) {
                $query->where(function ($query2) use ($filters) {
                    $query2->whereRaw('lower(shows_tickets.name) COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['search']).'%"')
                        ->orWhere('shows_tickets.email', 'like', '%'.$filters['search'].'%')
                        ->orWhere('shows_tickets.phone', 'like', '%'.$filters['search'].'%')
                        ->orWhere('shows_tickets.vat', 'like', '%'.$filters['search'].'%');
                });

            })
            ->when(isset($filters['used']) && $filters['used'], function ($query) {
                $query->where(function ($query2) {
                    $query2->where('shows_tickets.states_id', '<>', config('states.unused'))
                        ->orWhereIn('shows_tickets.types_id', [config('constants.tickets_types.protocol'), config('constants.tickets_types.ticket_office')]);
                });
            })
            ->when(isset($filters['validated']) && $filters['validated'], function ($query) {
                $query->whereNotNull('shows_tickets.used_at');
            })
            ->when(isset($filters['date']) && isset($filters['datetype']) && ! empty($filters['date']) && ! empty($filters['datetype']), function ($query) use ($filters) {
                return $query->whereBetween('shows_tickets.'.$filters['datetype'], $filters['date']);
            })
            ->when(isset($filters['seats_numbers']) && ! empty($filters['seats_numbers']), function ($query) use ($filters) {
                return $query->whereIn('shows_tickets.seat', $filters['seats_numbers']);
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    /**
     * Get shows tickets report
     *
     * @return mixed Collection
     */
    public static function emtGetReport(
        array $filters = []
    ) {
        $oQuery = static::select('s.id', 's.date', 'e.id as events_id', 'e.description->'.app()->getLocale().' as event')
            ->selectRaw('
            count(*) as tickets,
            sum(if(shows_tickets.states_id='.config('states.payed').', 1, 0)) as payed,
            sum(if(shows_tickets.types_id='.config('constants.tickets_types.protocol').', 1, 0)) as protocol,
            sum(if(shows_tickets.types_id='.config('constants.tickets_types.ticket_office').', 1, 0)) as ticket_office
        ')
            ->join('shows_tickets_types as t', 't.id', 'shows_tickets.types_id')
            ->join('shows_events_sessions as s', 's.id', 'shows_tickets.sessions_id')
            ->join('shows_events as e', 'e.id', 's.events_id')
            ->when(isset($filters['sessions_id']) && ! empty($filters['sessions_id']), function ($query) use ($filters) {
                return $query->where('shows_tickets.sessions_id', $filters['sessions_id']);
            })
            ->when(isset($filters['events_id']) && ! empty($filters['events_id']), function ($query) use ($filters) {
                return $query->where('e.id', $filters['events_id']);
            })
            ->when(isset($filters['shows_id']) && ! empty($filters['shows_id']), function ($query) use ($filters) {
                return $query->where('e.shows_id', $filters['shows_id']);
            })
            ->when(isset($filters['states_id']) && ! empty($filters['states_id']), function ($query) use ($filters) {
                return $query->where('shows_tickets.states_id', $filters['states_id']);
            })
            ->when(isset($filters['states_ids']) && ! empty($filters['states_ids']), function ($query) use ($filters) {
                return $query->whereIn('shows_tickets.states_id', $filters['states_ids']);
            })
            ->when(isset($filters['types_id']) && ! empty($filters['types_id']), function ($query) use ($filters) {
                return $query->where('shows_tickets.types_id', $filters['types_id']);
            })
            ->when(isset($filters['types_ids']) && ! empty($filters['types_ids']), function ($query) use ($filters) {
                return $query->whereIn('shows_tickets.types_id', $filters['types_ids']);
            });
        $oQuery->groupBy('s.id', 'events_id');
        $oQuery->orderBy('e.description->'.app()->getLocale(), 'asc');
        $oQuery->orderBy('s.date', 'asc');

        $records = $oQuery->get()->keyBy('id');

        return $records;
    }

    public function buy($tickets_amount, $selected = [])
    {
        $session = ShowsEventsSession::emtGet($this->sessions_id);
        $price = $session->shows_event->price;
        $previous_tickets = 0;
        $max_tickets = $session->shows_event->max_tickets;
        if ($this->types_id == config('constants.tickets_types.general')) {
            $previous_tickets = ShowsTicket::where('sessions_id', $this->sessions_id)
                ->where('vat', $this->vat)
                ->where('states_id', '<>', config('states.unused'))
                ->where('vat', $this->vat)
                ->count();
        }
        $lock_id = emt_sign(Carbon::now()->timestamp);
        if ($tickets_amount + $previous_tickets <= $max_tickets) {
            $updated = ShowsTicket::where('sessions_id', $this->sessions_id)->where('types_id', config('constants.tickets_types.general'))
                ->where('states_id', config('states.unused'))
                ->when(! empty($selected), function ($query) use ($selected) {
                    return $query->whereIn('id', $selected);
                })
                ->limit($tickets_amount)
                ->update([
                    'purchase_date' => ($price == 0) ? Carbon::now() : null,
                    'liberation_date' => Carbon::now()->addMinutes(15),
                    'users_id' => auth()->user()->id,
                    'lock_id' => $lock_id,
                    'states_id' => ($price > 0) ? config('states.pending') : config('states.payed'),
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'observations' => $this->observations,
                    'name' => $this->name,
                    'vat' => $this->vat,
                    // 'types_id' => $this->types_id,
                ]);
            if ($updated != $tickets_amount) {
                ShowsTicket::where('sessions_id', $this->sessions_id)
                    ->where('lock_id', $lock_id)
                    ->update([
                        'purchase_date' => null,
                        'liberation_date' => null,
                        'users_id' => 0,
                        'lock_id' => null,
                        'states_id' => config('states.unused'),
                        'email' => '',
                        'phone' => '',
                        'observations' => '',
                        'name' => '',
                        'vat' => '',
                    ]);

                return '0'; // Exceed tickets limit after check
            } else {
                return $lock_id; // Tickets locked ok
            }
        } else {
            return '-1'; // Exceed DNI/NIE tickets limit
        }
    }

    public function shows_events_session()
    {
        return $this->hasOne(ShowsEventsSession::class, 'id', 'sessions_id')->with('shows_event');
    }

    public function shows_tickets_type()
    {
        return $this->hasOne(ShowsTicketsType::class, 'id', 'types_id');
    }

    public function state()
    {
        return $this->hasOne(State::class, 'id', 'states_id');
    }
}
