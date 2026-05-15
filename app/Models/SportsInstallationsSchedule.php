<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class SportsInstallationsSchedule extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
        'from_hour' => 'datetime',
        'to_hour' => 'datetime',
    ];

    /**
     * Get sport installations schedules
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
        array $with = ['sports_installation']
    ) {

        $oQuery = static::select('sports_installations_schedules.*')
        ->selectRaw('count(rv.id) as reservations_count')
        ->leftJoin('sports_installations_resources_groups as g', 'g.installations_id', 'sports_installations_schedules.installations_id')
        ->leftJoin('sports_installations_resources as r', 'g.id', 'r.groups_id')
        ->leftJoin('sports_installations_reservations as rv', 'rv.resources_id', 'r.id')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('sports_installations_schedules.id', $model_id);
        })
        ->when(isset($filters['installations_id']) && !empty($filters['installations_id']), function($query) use ($filters) {
            return $query->where('sports_installations_schedules.installations_id', $filters['installations_id']);
        })
        ->when(isset($filters['day']) && !empty($filters['day']), function ($query) use ($filters) {
            $query->where('sports_installations_schedules.from_date', '<=',$filters['day'])
            ->where('sports_installations_schedules.to_date', '>=', $filters['day'])
            ->where('sports_installations_schedules.'.strtolower($filters['day']->shortEnglishDayOfWeek), 1)
            ->whereBetween('rv.from_date',  [$filters['day'],$filters['day']->endOfDay()]);
        })
        ->when(isset($filters['groups_id']) && !empty($filters['groups_id']), function($query) use ($filters) {
            return $query->where('r.groups_id', $filters['groups_id']);
        })
        ;

        $oQuery->groupBy('sports_installations_schedules.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    /**
     * Get available installations schedules
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

        $oQuery = static::select('sports_installations_schedules.*')
        ->selectRaw('count(rv.id) as reservations_count,
            count(distinct r.id) as resources_count')
        ->join('sports_installations_resources_groups as g', 'g.installations_id', 'sports_installations_schedules.installations_id')
        ->leftJoin('sports_installations_resources as r', 'g.id', 'r.groups_id')
        ->leftJoin('sports_installations_reservations as rv', function($join) use ($filters) {
            $join->on('rv.resources_id', 'r.id')
            ->when(isset($filters['day']) && !empty($filters['day']), function ($query) use ($filters) {
                $day_end = $filters['day']->clone()->endOfDay();
                $query->whereBetween('rv.from_date',  [$filters['day'],$day_end]);
            });
        })
        ->where('sports_installations_schedules.installations_id', $filters['installations_id'])
        ->where('r.states_id', config('states.active'))
        ->whereNotExists(function ($query) use ($filters) {
            $date = $filters['day']->startOfDay();
            $query->select(DB::raw(1))
            ->from('sports_installations_holidays as h')
            ->whereColumn('h.installations_id', 'g.installations_id')
            ->where('h.date', $date);
        })
        ->when(isset($filters['day']) && !empty($filters['day']), function ($query) use ($filters) {
            $query->where('sports_installations_schedules.from_date', '<=',$filters['day'])
            ->where('sports_installations_schedules.to_date', '>=', $filters['day'])
            ->where('sports_installations_schedules.'.strtolower($filters['day']->shortEnglishDayOfWeek), 1);
        })
        ->when(isset($filters['groups_id']) && !empty($filters['groups_id']), function($query) use ($filters) {
            return $query->where('r.groups_id', $filters['groups_id']);
        })
        ->when(isset($filters['rented']) && !empty($filters['rented']), function($query) {
            $query->where('r.leasable', 1);
        })
        ;
        $oQuery->groupBy('sports_installations_schedules.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return $oQuery->with(['sports_installation'])->get()->keyBy('id');
    }

    public function save($options = array(), $do_log = true)
    {
        // Check if another schedule intersect with this
        $schedules = static::
        where('id', '<>', $this->id)
        ->where('installations_id', $this->installations_id)
        ->where(function($query) {
            $query->where(function($query) {
                $query->whereBetween('from_date', [$this->from_date, $this->to_date])
                ->where(function($query) {
                    $query->whereBetween('from_hour', [$this->from_hour, $this->to_hour])
                        ->orWhereBetween('to_hour', [$this->from_hour, $this->to_hour])
                    ;
                })
                ->where(function ($query) {
                    $query->when($this->mon, function($query) {
                        $query->orWhere('mon', 1);
                    })
                    ->when($this->tue, function($query) {
                        $query->orWhere('tue', 1);
                    })
                    ->when($this->wed, function($query) {
                        $query->orWhere('wed', 1);
                    })
                    ->when($this->thu, function($query) {
                        $query->orWhere('thu', 1);
                    })
                    ->when($this->fri, function($query) {
                        $query->orWhere('fri', 1);
                    })
                    ->when($this->sat, function($query) {
                        $query->orWhere('sat', 1);
                    })
                    ->when($this->sun, function($query) {
                        $query->orWhere('sun', 1);
                    });
                });
            })
            ->orwhere(function($query) {
                $query->whereBetween('to_date', [$this->from_date, $this->to_date])
                ->where(function($query) {
                    $query->whereBetween('from_hour', [$this->from_hour, $this->to_hour])
                        ->orWhereBetween('to_hour', [$this->from_hour, $this->to_hour])
                    ;
                })
                ->where(function ($query) {
                    $query->when($this->mon, function($query) {
                        $query->orWhere('mon', 1);
                    })
                    ->when($this->tue, function($query) {
                        $query->orWhere('tue', 1);
                    })
                    ->when($this->wed, function($query) {
                        $query->orWhere('wed', 1);
                    })
                    ->when($this->thu, function($query) {
                        $query->orWhere('thu', 1);
                    })
                    ->when($this->fri, function($query) {
                        $query->orWhere('fri', 1);
                    })
                    ->when($this->sat, function($query) {
                        $query->orWhere('sat', 1);
                    })
                    ->when($this->sun, function($query) {
                        $query->orWhere('sun', 1);
                    });
                });
                // ->where(function ($query) {
                //     $query->where('mon', $this->mon)
                //         ->orWhere('tue', $this->tue)->orWhere('wed', $this->wed)->orWhere('thu', $this->thu)
                //         ->orWhere('fri', $this->fri)->orWhere('sat', $this->sat)->orWhere('sun', $this->sun);
                // });
            })
            ->orwhere(function($query) {
                $query->whereRaw("'".$this->from_date->format('Y-m-d')."' between from_date and to_date")
                ->where(function($query) {
                    $query->whereBetween('from_hour', [$this->from_hour, $this->to_hour])
                        ->orWhereBetween('to_hour', [$this->from_hour, $this->to_hour])
                    ;
                })
                ->where(function ($query) {
                    $query->when($this->mon, function($query) {
                        $query->orWhere('mon', 1);
                    })
                    ->when($this->tue, function($query) {
                        $query->orWhere('tue', 1);
                    })
                    ->when($this->wed, function($query) {
                        $query->orWhere('wed', 1);
                    })
                    ->when($this->thu, function($query) {
                        $query->orWhere('thu', 1);
                    })
                    ->when($this->fri, function($query) {
                        $query->orWhere('fri', 1);
                    })
                    ->when($this->sat, function($query) {
                        $query->orWhere('sat', 1);
                    })
                    ->when($this->sun, function($query) {
                        $query->orWhere('sun', 1);
                    });
                });
                // ->where(function ($query) {
                //     $query->where('mon', $this->mon)
                //         ->orWhere('tue', $this->tue)->orWhere('wed', $this->wed)->orWhere('thu', $this->thu)
                //         ->orWhere('fri', $this->fri)->orWhere('sat', $this->sat)->orWhere('sun', $this->sun);
                // });
            })
            ;
        })->get();

        if (length($schedules)>0) {
            return false;
        } else {
            parent::save($options); // Calls Default Save
            return true;
        }
    }

    public function sports_installation() {
        return $this->hasOne(SportsInstallation::class, 'id', 'installations_id');
    }

}
