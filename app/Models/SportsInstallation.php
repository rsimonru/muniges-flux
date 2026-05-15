<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SportsInstallation extends Model
{
    use HasFactory;
    use WithExtensions;
    use HasTranslations;

    public $translatable = ['name', 'information'];

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
        array $with = ['sports_installations_resources_groups','sports_installations_schedules']
    ) {

        $oQuery = static::select('sports_installations.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('sports_installations.id', $model_id);
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('sports_installations.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['name']) && !empty($filters['name']), function($query) use ($filters) {
            $query->whereRaw('lower(sports_installations.name->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['name']).'%"');
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    /**
     * Get available sports installations
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

        $oQuery = static::select('sports_installations.*')
        ->join('sports_installations_resources_groups as g', 'g.installations_id', 'sports_installations.id')
        ->join('sports_installations_resources as r', 'r.groups_id','g.id')
        ->join('sports_installations_schedules as s', function ($join) use ($filters) {
            $join->on('s.installations_id', 'g.installations_id')
            ->where('s.to_date', '>=', Carbon::today());
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('sports_installations.townhalls_id', $filters['townhalls_id']);
        })
        ->where('r.states_id', config('states.active'))
        ->when($filters['rented'], function($query) {
            return $query->where('r.leasable', 1);
        })
        ;
        $oQuery->groupBy('sports_installations.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return $oQuery->with(['sports_installations_resources_groups','sports_installations_schedules'])->get()->keyBy('id');
    }

    public function delete($do_log = true) {
        SportsInstallationsResourcesGroup::where('installations_id', $this->id)->delete();
        parent::delete();
    }

    public function sports_installations_resources_groups() {
        return $this->hasMany(SportsInstallationsResourcesGroup::class, 'installations_id', 'id');
    }
    public function sports_installations_schedules() {
        return $this->hasMany(SportsInstallationsSchedule::class, 'installations_id', 'id');
    }

}
