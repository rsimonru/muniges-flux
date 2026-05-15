<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class ShowsRoom extends Model
{
    use HasFactory;
    use WithExtensions;
    use HasTranslations;

    protected $casts = [
        'seats_map' => 'json',
        'zones' => 'json',
    ];
    public $translatable = ['description'];

    /**
     * Get shows rooms
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

        $oQuery = static::select('shows_rooms.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('shows_rooms.id', $model_id);
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('shows_rooms.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['description']) && !empty($filters['description']), function($query) use ($filters) {
            $query->whereRaw('lower(shows_rooms.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['description']).'%"');
        })
        ->when(isset($filters['active']) && !empty($filters['active']), function($query) use ($filters) {
            return $query->where('shows_rooms.active', $filters['active']);
        })
        ;
        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function rooms_events() {
        return $this->hasMany(ShowsEvent::class, 'rooms_id', 'id');
    }
}
