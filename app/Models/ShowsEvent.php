<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShowsEvent extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['description'];

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
        'tickets_from_date' => 'datetime',
        'tickets_to_date' => 'datetime',
    ];

    /**
     * Get shows events
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['shows_events_sessions']
    ) {

        $oQuery = static::select('shows_events.*')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('shows_events.id', $model_id);
            })
            ->when(isset($filters['shows_id']) && ! empty($filters['shows_id']), function ($query) use ($filters) {
                return $query->where('shows_events.shows_id', $filters['shows_id']);
            })
            ->when(isset($filters['description']) && ! empty($filters['description']), function ($query) use ($filters) {
                $query->whereRaw('lower(shows_events.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['description']).'%"');
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function show()
    {
        return $this->hasOne(Show::class, 'id', 'shows_id');
    }

    public function room()
    {
        return $this->hasOne(ShowsRoom::class, 'id', 'rooms_id');
    }

    public function shows_events_sessions()
    {
        return $this->hasMany(ShowsEventsSession::class, 'events_id', 'id')->orderBy('shows_events_sessions.date', 'asc');
    }
}
