<?php

namespace App\Models;

use App\Models\Scopes\TownHallScope;
use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SportsEvent extends Model
{
    use HasFactory;
    use WithExtensions;
    use HasTranslations;

    public $translatable = ['name', 'information'];

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
        'inscription_from' => 'datetime',
        'inscription_to' => 'datetime',
    ];
    protected $fillable = [
        'name',
        'information',
        'from_date',
        'to_date',
        'inscription_from',
        'inscription_to',
        'price',
        'procedures_id',
        'inscr_templates_id',
        'bonus_templates_id',
        'third_templates_id',
        'payme_templates_id',
        'dorsal_templates_id',
        'pay_reg_by_activity',
        'townhalls_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TownHallScope);
    }

    /**
     * Get sport events
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
        array $with = ['sports_events_activities']
    ) {

        $oQuery = static::select('sports_events.*')
        ->when(!empty($model_id), function($query) use ($model_id) {
            return $query->where('sports_events.id', $model_id);
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('sports_events.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['name']) && !empty($filters['name']), function($query) use ($filters) {
            $query->whereRaw('lower(sports_events.name->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['name']).'%"');
        })
        ->when(isset($filters['date']) && isset($filters['datetype']) && !empty($filters['date']) && !empty($filters['datetype']), function($query) use ($filters) {
            return $query->whereBetween('sports_events.'.$filters['datetype'], $filters['date']);
        })
        ->when(isset($filters['inscriptions_active']), function ($query) {
            return $query->where(function ($query) {
                $query->where('sports_events.inscription_from', '<=', Carbon::today())
                    ->where('sports_events.inscription_to', '>=', Carbon::today());
            });
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            return $query->where(function ($query) use ($filters) {
                $query->whereRaw('lower(sports_events.name->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['search']).'%"');
            });
        });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function delete($do_log = true) {
        SportsEventsActivity::where('events_id', $this->id)->delete();
        SportsEventsPayment::where('events_id', $this->id)->delete();
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
                $event = static::find($id);
                $event->sequential++;
                $sequential = $event->sequential;
                $event->save([],false);
            }, 5);
        }
        return $sequential;
    }

    public function sports_events_activities() {
        return $this->hasMany(SportsEventsActivity::class, 'events_id', 'id');
    }
    public function sports_events_payments() {
        return $this->hasMany(SportsEventsPayment::class, 'events_id', 'id');
    }
    public function townhall() {
        return $this->belongsTo(TownHall::class, 'townhalls_id', 'id');
    }

}
