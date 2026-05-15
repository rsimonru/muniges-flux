<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class StaffSigning extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'date' => 'date',
        'sign_at' => 'datetime',
        'clock_out_at' => 'datetime',
        'sign_at_out' => 'datetime',
        'sign_at_out' => 'datetime',
        'data' => 'json',
    ];

    protected $attributes = [
        'created_user' => null,
        'updated_at' => null,
    ];

    /**
     * Get signings.
     *
     * @param int $iModels_id
     * @param int $iRecordsInPage
     * @param array $aSort (attribute => 'asc'/'desc')
     * @param array $aParams
     * @return mixed Colletion
     *
     */
    public static function emtGet(
        ?int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        ?array $filters = [],
        array $with = []
    ) {

        $oQuery = static::select('staff_signings.*')
        ->leftJoin('staff_employees as e', 'e.id', 'staff_signings.employees_id')
        ->when($model_id > 0, function ($query) use ($model_id) {
            return $query->where('staff_signings.id', $model_id);
        });

        $oQuery = static::emtApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        // $oQuery->dd();
        $result = static::getModelData($oQuery, $model_id, $records_in_page, $with);
        return $result;
    }
    public static function emtApplyFilters(
        $query,
        array $filters = []
    )
    {
        $query = $query->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function ($query) use ($filters) {
            return $query->where('staff_signings.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['employees_id']) && !empty($filters['employees_id']), function ($query) use ($filters) {
            return $query->where('staff_signings.employees_id', $filters['employees_id']);
        })
        ->when(isset($filters['provider_schedule_code']) && !empty($filters['provider_schedule_code']), function ($query) use ($filters) {
            return $query->where('staff_signings.provider_schedule_code', $filters['provider_schedule_code']);
        })
        ->when(isset($filters['wcenter_id']) && !empty($filters['wcenter_id']), function ($query) use ($filters) {
            return $query->where('staff_signings.wcenter_id', $filters['wcenter_id']);
        })
        ->when(isset($filters['out_pending']) && !empty($filters['out_pending']), function ($query) use ($filters) {
            return $query->where('staff_signings.in', 1)->whereNull('staff_signings.out');
        })
        ->when(isset($filters['last_sign']) && !empty($filters['last_sign']), function ($query) use ($filters) {
            return $query->where('staff_signings.in', 1)->where('staff_signings.out', 1);
        })
        ->when(isset($filters['extra_hour']), function ($query) use ($filters) {
            return $query->where('staff_signings.extra_hour', $filters['extra_hour']);
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            $query->where(function($query2) use ($filters) {
                $query2->whereRaw("concat(e.name, ' ', e.surname) like '%".$filters['search']."%'")
                ->orWhere('e.email', 'like', '%'.$filters['search'].'%');
            });

        })
        ->when(isset($filters['date']) && !empty($filters['date']) && !empty($filters['date'][0]), function ($query) use ($filters) {
            $from = (new Carbon($filters['date'][1]))->startOfDay();
            $to = (new Carbon($filters['date'][2]))->endOfDay();
            return $query->whereBetween('staff_signings.' . $filters['date'][0], [$from, $to]);
        });

        return $query;
    }
    public static function emtGetSummary(
        ?array $filters = [],
        int $records_in_page = 0,
        ?array $group_by = [],
        ?array $sort = []
    )
    {
        $oQuery = static::selectRaw('
            sum(if(staff_signings.sign_at, 1, 0)) as signed_days,
            sum(staff_signings.signing_time) as signing_time,
            sum(staff_signings.signing_balance) as signing_balance
        ');

        $oQuery = static::emtApplyFilters($oQuery, $filters);

        if (!empty($group_by)) {
            foreach ($group_by as $group) {
                if ($group == 'staff_signings.employees_id') {
                    $oQuery->addSelect('staff_signings.employees_id');
                }
                if ($group == 'month') {
                    $oQuery->addSelect(DB::raw('month(staff_signings.date) as month'));
                }
                $oQuery->groupBy($group);
            }
            // $oQuery->dd();

            foreach ($sort as $key => $value) {
                $oQuery->orderBy($key, $value);
            }
            return static::getModelData($oQuery, 0, $records_in_page, [], null);
        } else {
            return $oQuery->get()->first();
        }

    }

    public function save($options = array(), $do_log = true)
    {
        parent::save($options, $do_log); // Calls Default Save

        return $this->id;
    }

    public function employee()
    {
        return $this->hasOne(StaffEmployee::class, 'id', 'employees_id');
    }
    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user', 'id');
    }
    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_user', 'id');
    }
}
