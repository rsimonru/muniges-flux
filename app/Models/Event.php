<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['name', 'information'];

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
        'inscription_from' => 'datetime',
        'inscription_to' => 'datetime',
    ];

    /**
     * Get events
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

        $oQuery = static::select('events.*')
            ->when(! empty($model_id), function ($query) use ($model_id) {
                return $query->where('events.id', $model_id);
            })
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
                return $query->where('events.townhalls_id', $filters['townhalls_id']);
            })
            ->when(isset($filters['name']) && ! empty($filters['name']), function ($query) use ($filters) {
                $query->whereRaw('lower(events.name->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['name']).'%"');
            })
            ->when(isset($filters['date']) && isset($filters['datetype']) && ! empty($filters['date']) && ! empty($filters['datetype']), function ($query) use ($filters) {
                return $query->whereBetween('events.'.$filters['datetype'], $filters['date']);
            })
            ->when(isset($filters['inscriptions_active']), function ($query) {
                return $query->where(function ($query) {
                    $query->where('events.inscription_from', '<=', Carbon::today())
                        ->where('events.inscription_to', '>=', Carbon::today());
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
        EventsActivity::where('events_id', $this->id)->delete();
        EventsPayment::where('events_id', $this->id)->delete();
        parent::delete();
    }

    /**
     * Get sequential
     *
     * @return int sequential
     */
    public function getSequential()
    {
        $sequential = 0;
        if (! empty($this->id)) {
            $id = $this->id;
            DB::transaction(function () use (&$sequential, $id) {
                $event = static::find($id);
                $event->sequential++;
                $sequential = $event->sequential;
                $event->save([], false);
            }, 5);
        }

        return $sequential;
    }

    public function events_activities()
    {
        return $this->hasMany(EventsActivity::class, 'events_id', 'id');
    }

    public function events_payments()
    {
        return $this->hasMany(EventsPayment::class, 'events_id', 'id');
    }
}
