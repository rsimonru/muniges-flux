<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShowsEventsSession extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Get shows events sessions
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
        array $with = ['shows_event']
    ) {

        $oQuery = static::select('shows_events_sessions.*')
        ->selectRaw('count(t.id) as count_tickets, sum(if(t.types_id=1,1,0)) as count_general, sum(if(t.types_id=2,1,0)) as count_protocol, sum(if(t.types_id=3,1,0)) as count_ticket_office')
        ->selectRaw('sum(if(t.states_id in (?),1,0)) as count_used',[config('states.payed').','.config('states.pending').','.config('states.reserved')])
        ->join('shows_events as e', 'e.id', 'shows_events_sessions.events_id')
        ->join('shows as s', 's.id', 'e.shows_id')
        ->leftJoin('shows_tickets as t', 't.sessions_id', 'shows_events_sessions.id')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('shows_events_sessions.id', $model_id);
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('s.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['events_id']) && !empty($filters['events_id']), function($query) use ($filters) {
            return $query->where('shows.events_id', $filters['events_id']);
        })
        ->when(isset($filters['shows_id']) && !empty($filters['shows_id']), function($query) use ($filters) {
            return $query->where('e.shows_id', $filters['shows_id']);
        })
        ->when(isset($filters['date']) && !empty($filters['date']), function($query) use ($filters) {
            return $query->whereBetween('shows.date', $filters['date']);
        })
        ;

        $oQuery->groupBy('shows_events_sessions.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function delete($do_log = true) {
        ShowsTicket::where('sessions_id', $this->id)->delete();
        parent::delete();
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
                $session = static::find($id);
                $session->sequential++;
                $sequential = $session->sequential;
                $session->save([],false);
            }, 5);
        }
        return $sequential;
    }

    public function shows_event() {
        return $this->hasOne(ShowsEvent::class, 'id', 'events_id')->with(['show', 'room']);
    }
}
