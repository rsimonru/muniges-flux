<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;
use Illuminate\Support\Facades\DB;

class SportsInstallationsResourcesGroup extends Model
{
    use HasFactory;
    use WithExtensions;
    use HasTranslations;

    public $translatable = ['name'];

    /**
     * Get sport installations
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
        array $with = ['sports_installations_resources','sports_installation','sports_installations_groups_slots']
    ) {

        $oQuery = static::select('sports_installations_resources_groups.*')
        //->selectRaw('count(r.id) as resources_count, CONCAT("[", GROUP_CONCAT(r.id), "]") as resources')
        ->leftJoin('sports_installations_resources as r', 'r.groups_id', 'sports_installations_resources_groups.id')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('sports_installations_resources_groups.id', $model_id);
        })
        ->when(isset($filters['installations_id']) && !empty($filters['installations_id']), function($query) use ($filters) {
            return $query->where('sports_installations_resources_groups.installations_id', $filters['installations_id']);
        })
        ->when(isset($filters['name']) && !empty($filters['name']), function($query) use ($filters) {
            $query->whereRaw('lower(sports_installations_resources_groups.name->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['name']).'%"');
        })
        ->when(isset($filters['active']), function($query) {
            $query->where('r.states_id', config('states.active'));
        })
        ->when(isset($filters['rented']) && !empty($filters['rented']), function($query) {
            $query->where('r.leasable', 1);
        })
        ;
        $oQuery->groupBy('sports_installations_resources_groups.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    /**
     * Get available sports installations resources groups
     *
     * @param array $sort (attribute => 'asc'/'desc')
     * @param array $filters
     * @return mixed Collection
     *
     */
    public static function emtGetAvailable(
        array $sort = [],
        array $filters = []
    ) {

        $oQuery = static::select('sports_installations_resources_groups.*')
        //->selectRaw('count(r.id) as resources_count, CONCAT("[", GROUP_CONCAT(r.id), "]") as resources')
        ->join('sports_installations_resources as r', 'r.groups_id', 'sports_installations_resources_groups.id')
        ->join('sports_installations_schedules as s', 's.installations_id', 'sports_installations_resources_groups.installations_id')
        ->when(isset($filters['day']) && !empty($filters['day']), function ($query) use ($filters) {
            $query->where('s.from_date', '<=',$filters['day'])
            ->where('s.to_date', '>=', $filters['day'])
            ->where('s.'.strtolower($filters['day']->shortEnglishDayOfWeek), 1)
            ;
        })
        ->when(isset($filters['day_without_dow']) && !empty($filters['day']), function ($query) use ($filters) {
            $query->where('s.from_date', '<=',$filters['day'])
            ->where('s.to_date', '>=', $filters['day']);
        })
        ->where('sports_installations_resources_groups.installations_id', $filters['installations_id'])
        ->where('r.states_id', config('states.active'))
        ->whereNotExists(function ($query) use ($filters) {
            $date = $filters['day']->startOfDay();
            $query->select(DB::raw(1))
            ->from('sports_installations_holidays as h')
            ->whereColumn('h.installations_id', 'sports_installations_resources_groups.installations_id')
            ->where('h.date', $date);
        })
        ->when(isset($filters['rented']) && !empty($filters['rented']), function($query) {
            $query->where('r.leasable', 1);
        })
        // ->whereExists(function ($query) {
        //     $query->select(DB::raw(1))
        //         ->from('sports_installations_resources as r')
        //         ->whereColumn('r.groups_id', 'sports_installations_resources_groups.id')
        //         ->where('r.states_id', config('states.active'))
        //         ;
        // })
        // ->when(isset($filters['rented']) && !empty($filters['rented']), function($query) {
        //     $query->whereExists(function ($query2) {
        //         $query2->select(DB::raw(1))
        //             ->from('sports_installations_resources as r')
        //             ->whereColumn('r.groups_id', 'sports_installations_resources_groups.id')
        //             ->where('r.leasable', 1)
        //             ;
        //     });
        // })
        ;
        $oQuery->groupBy('sports_installations_resources_groups.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        // $oQuery->dd();
        return $oQuery->with(['sports_installations_resources','sports_installation','sports_installations_groups_slots'])->get()->keyBy('id');
    }

    public function delete($do_log = true) {
        SportsInstallationsResource::where('groups_id', $this->id)->delete();
        parent::delete();
    }

    public function sports_installations_resources() {
        return $this->hasMany(SportsInstallationsResource::class, 'groups_id', 'id');
    }
    public function sports_installation() {
        return $this->hasOne(SportsInstallation::class, 'id', 'installations_id');
    }
    public function sports_installations_groups_slots() {
        return $this->hasOne(SportsInstallationsGroupsSlot::class, 'value', 'slot');
    }

}
