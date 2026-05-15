<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Show extends Model
{
    use HasFactory;
    use WithExtensions;
    use HasTranslations;

    public $translatable = ['description', 'information'];

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
    ];

    /**
     * Get shows
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
        array $with = ['shows_events']
    ) {

        $oQuery = static::select('shows.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('shows.id', $model_id);
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('shows.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['description']) && !empty($filters['description']), function($query) use ($filters) {
            $query->whereRaw('lower(shows.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['description']).'%"');
        })
        ->when(isset($filters['date']) && isset($filters['datetype']) && !empty($filters['date']) && !empty($filters['datetype']), function($query) use ($filters) {
            return $query->whereBetween('shows.'.$filters['datetype'], $filters['date']);
        })
        ->when(isset($filters['shows_active']), function ($query) {
            $query->where('shows.to_date', '>=', Carbon::today());
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function shows_events() {
        return $this->hasMany(ShowsEvent::class, 'shows_id', 'id');
    }

}
