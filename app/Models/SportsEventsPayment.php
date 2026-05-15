<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class SportsEventsPayment extends Model
{
    use HasFactory;
    use WithExtensions;
    use HasTranslations;

    public $translatable = ['description'];

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
    ];

    protected $fillable = [
        'description',
        'amount',
        'events_id',
        'from_date',
        'to_date',
        'types_id',
    ];

    /**
     * Get sport events payments
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
        array $with = ['sports_event', 'type']
    ) {

        $oQuery = static::select('sports_events_payments.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('sports_events_payments.id', $model_id);
        })
        ->when(isset($filters['events_id']) && !empty($filters['events_id']), function($query) use ($filters) {
            return $query->where('sports_events_payments.events_id', $filters['events_id']);
        })
        ->when(isset($filters['types_id']) && !empty($filters['types_id']), function($query) use ($filters) {
            return $query->where('sports_events_payments.types_id', $filters['types_id']);
        })
        ->when(isset($filters['description']) && !empty($filters['description']), function($query) use ($filters) {
            $query->whereRaw('lower(sports_events_payments.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['description']).'%"');
        })
        ->when(isset($filters['date']) && !empty($filters['date']), function ($query) use ($filters) {
            return $query->where(function ($query) use ($filters){
                $query->whereBetween('sports_events_payments.from_date', $filters['date'])
                    ->orWhereBetween('sports_events_payments.to_date', $filters['date']);
            });
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function sports_event() {
        return $this->hasOne(SportsEvent::class, 'id', 'events_id');
    }
    public function type() {
        return $this->hasOne(SportsEventsPaymentsType::class, 'id', 'types_id');
    }
    public function sports_events_registrations_payments() {
        return $this->hasMany(SportsEventsRegistrationsPayment::class, 'payments_id', 'id');
    }

}
