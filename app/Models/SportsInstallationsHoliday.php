<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SportsInstallationsHoliday extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get sport installations holidays
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

        $oQuery = static::select('sports_installations_holidays.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('sports_installations_holidays.id', $model_id);
        })
        ->when(isset($filters['installations_id']) && !empty($filters['installations_id']), function($query) use ($filters) {
            return $query->where('sports_installations_holidays.installations_id', $filters['installations_id']);
        })
        ->when(isset($filters['date']) && !empty($filters['date']), function($query) use ($filters) {
            return $query->whereBetween('sports_installations_holidays.date', $filters['date']);
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    /**
     * Get sport installations year holidays
     *
     * @param int $model_id
     * @param int $records_in_page
     * @param array $sort (attribute => 'asc'/'desc')
     * @param array $filters
     * @return mixed Collection
     *
     */
    public static function emtGetYear(
        int $model_id=0,
        int $records_in_page = 0,
        array $filters = []
    ) {

        $oQuery = static::selectRaw('year(sports_installations_holidays.`date`) as `year`, count(*) as `holidays`')
        ->when(isset($filters['installations_id']) && !empty($filters['installations_id']), function($query) use ($filters) {
            return $query->where('sports_installations_holidays.installations_id', $filters['installations_id']);
        })
        ->when(isset($filters['year']) && !empty($filters['year']), function($query) use ($filters) {
            $from = Carbon::create($filters['year'])->startOfDay();
            $to = Carbon::create($filters['year'])->endOfYear();
            return $query->whereBetween('sports_installations_holidays.date', [$from, $to]);
        })
        ;

        $oQuery->groupBy('year');
        $oQuery->orderBy('year', 'desc');

        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, [], 'year');
    }

    /**
	 * Overload model save.
	 */
    public function save (array $options = array(), $do_log = true)
    {
        $holidays = static::where('installations_id',$this->installations_id)->where('date',$this->date)->get();
        if (length($holidays)==0) {
            parent::save($options); // Calls Default Save
            return true;
        }
        return false;
    }

}
