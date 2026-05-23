<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServicesEvent extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    /**
     * Get services events
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['services_category', 'state', 'assigned_user']
    ) {

        $oQuery = static::select('services_events.*')
            ->join('services_categories as c', 'c.id', 'services_events.categories_id')
            ->join('states as s', 's.id', 'services_events.states_id')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('services_events.id', $model_id);
            })
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
                return $query->where('services_events.townhalls_id', $filters['townhalls_id']);
            })
            ->when(isset($filters['categories_id']) && ! empty($filters['categories_id']), function ($query) use ($filters) {
                return $query->where('services_events.categories_id', $filters['categories_id']);
            })
            ->when(isset($filters['states_ids']) && ! empty($filters['states_ids']), function ($query) use ($filters) {
                return $query->whereIn('services_events.states_id', $filters['states_ids']);
            })
            ->when(isset($filters['description']) && ! empty($filters['description']), function ($query) use ($filters) {
                $query->where('services_events.description', 'like', '%'.$filters['description'].'%');
            })
            ->when(isset($filters['created_at']) && ! empty($filters['created_at']), function ($query) use ($filters) {
                return $query->whereBetween('services_events.created_at', $filters['created_at']);
            })
            ->when(isset($filters['assigned_users']) && ! empty($filters['assigned_users']), function ($query) use ($filters) {
                return $query->whereIn('services_events.assigned_user_id', $filters['assigned_users']);
            })
            ->when(session('user_level') == 2, function ($query) {
                return $query->whereExists(function ($query2) {
                    $query2->select(DB::raw(1))
                        ->from('services_events_notes as sen')
                        ->whereColumn('sen.events_id', 'services_events.id')
                        ->where('sen.assigned_user_id', auth()->user()->id);
                });
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function delete($do_log = true)
    {

        $notes = $this->services_events_notes;
        foreach ($notes as $note) {
            Storage::delete($note->photo);
        }
        ServicesEventsNote::where('events_id', $this->id)->delete();

        parent::delete();

    }

    public function services_events_notes()
    {
        return $this->hasMany(ServicesEventsNote::class, 'events_id', 'id')
            ->orderBy('services_events_notes.created_at', 'desc');
    }

    public function services_category()
    {
        return $this->hasOne(ServicesCategory::class, 'id', 'categories_id');
    }

    public function state()
    {
        return $this->hasOne(State::class, 'id', 'states_id');
    }

    public function assigned_user()
    {
        return $this->hasOne(User::class, 'id', 'assigned_user_id');
    }
}
