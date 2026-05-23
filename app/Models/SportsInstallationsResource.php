<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class SportsInstallationsResource extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['name'];

    /**
     * Get sports installations resources
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['sports_installations_resources_group', 'state']
    ) {

        $oQuery = static::select('sports_installations_resources.*')
            ->selectRaw('count(r.id) as reservations_count')
            ->join('sports_installations_resources_groups as g', 'g.id', 'sports_installations_resources.groups_id')
            ->leftJoin('sports_installations_reservations as r', 'r.resources_id', 'sports_installations_resources.id')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('sports_installations_resources.id', $model_id);
            })
            ->when(isset($filters['groups_id']) && ! empty($filters['groups_id']), function ($query) use ($filters) {
                return $query->where('sports_installations_resources.groups_id', $filters['groups_id']);
            })
            ->when(isset($filters['installations_id']) && ! empty($filters['installations_id']), function ($query) use ($filters) {
                return $query->where('g.installations_id', $filters['installations_id']);
            })
            ->when(isset($filters['name']) && ! empty($filters['name']), function ($query) use ($filters) {
                $query->whereRaw('lower(sports_installations_resources.name->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['name']).'%"');
            });
        $oQuery->groupBy('sports_installations_resources.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    /**
     * Get available sports installations resources
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGetAvailable(
        array $sort = [],
        array $filters = []
    ) {

        $oQuery = static::select('sports_installations_resources.*')
            ->join('sports_installations_resources_groups as g', 'g.id', 'sports_installations_resources.groups_id')
            ->join('sports_installations_schedules as s', function ($join) use ($filters) {
                $join->on('s.installations_id', 'g.installations_id')
                    ->where('s.from_date', '<=', $filters['day'])
                    ->where('s.to_date', '>=', $filters['day'])
                    ->where('s.'.strtolower($filters['day']->shortEnglishDayOfWeek), 1);
            })
            ->where('sports_installations_resources.groups_id', $filters['groups_id'])
            ->where('sports_installations_resources.states_id', config('states.active'))
            ->when($filters['rented'], function ($query) {
                return $query->where('sports_installations_resources.leasable', 1);
            })
            ->whereNotExists(function ($query) use ($filters) {
                $date = $filters['day']->startOfDay();
                $query->select(DB::raw(1))
                    ->from('sports_installations_holidays as h')
                    ->whereColumn('h.installations_id', 'g.installations_id')
                    ->where('h.date', $date);
            });
        $oQuery->groupBy('sports_installations_resources.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return $oQuery->with(['sports_installations_resources_group', 'state'])->get()->keyBy('id');
    }

    public function sports_installations_resources_group()
    {
        return $this->hasOne(SportsInstallationsResourcesGroup::class, 'id', 'groups_id')->with('sports_installation');
    }

    public function state()
    {
        return $this->hasOne(State::class, 'id', 'states_id');
    }
}
