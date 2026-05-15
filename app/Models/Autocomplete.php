<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use App\Models\Role;
use App\Models\Color;
use App\Models\Province;

class Autocomplete extends Model
{

    public static function get_day($d) {
        $aDays = [
            1 => ['es' => 'Lunes', 'en' => 'Monday'],
            2 => ['es' => 'Martes', 'en' => 'Tuesday'],
            3 => ['es' => 'Miércoles', 'en' => 'Wednesday'],
            4 => ['es' => 'Jueves', 'en' => 'Thursday'],
            5 => ['es' => 'Viernes', 'en' => 'Friday'],
            6 => ['es' => 'Sábado', 'en' => 'Saturday'],
            7 => ['es' => 'Domingo', 'en' => 'Sunday'],
        ];
        return ['value' => $d, "option" => $aDays[$d][app()->getLocale()]];
    }

    /**
     * Get select.
     *
     * @param string $vcSelect
     * @param string $parameter1
     * @param string $parameter2
     * @param string $parameter3
     * @param string $parameter4
     * @return mixed Colletion
     *
     */
    public static function emtGet(
        string $ac_query,
        $search,
        $maxRecords,
        $initOptions,
        $parameter1 = '',
        $parameter2 = '',
        $parameter3 = '',
        $parameter4 = ''
    ) {
        $oRecords = null;
        $oInitialRecords = [];
        $maxRecords = ($maxRecords == 0) ? config('constants.pagination.DEFAULT_PAGE_RECORDS') : $maxRecords;
        if (!empty($initOptions)) {
            if (is_array($initOptions)) {
                $initOptions = $initOptions;
            } else {
                $initOptions = explode(',', $initOptions);
                if (empty($initOptions)) {
                    $initOptions = [];
                }
            }
        } else {
            $initOptions = [];
        }

        switch ($ac_query) {
            case "roles":
                $query = Role::select('roles.id as value', 'name as text')
                    ->selectRaw('null as `data`');
                $oRecords = $query->clone();
                $oRecords = $oRecords->when(!empty($search), function ($query) use ($search) {
                        return $query->where(function ($query) use ($search) {
                            $query->where('name', 'like', $search);
                        });
                    })
                    ->when(!empty($parameter1), function ($query) use ($parameter1) {
                        $query->where('level', '<=', $parameter1);
                    })
                    ->whereNotIn('id', $initOptions)
                    ->orderBy('name', 'asc')
                    ->paginate($maxRecords);

                if (!empty($initOptions)) {
                    $oInitialRecords = $query
                        ->whereIn('id', $initOptions)
                        ->orderBy('name', 'asc')
                        ->paginate($maxRecords);
                }
                foreach($oRecords as $key => $option){
                    $oRecords[$key]['text'] = __('roles.'.$oRecords[$key]['text']);
                }
                foreach($oInitialRecords as $key => $option){
                    $oInitialRecords[$key]['text'] = __('roles.'.$oInitialRecords[$key]['text']);
                }
                break;
            case "states":
                $query = State::select('states.id as value', 'states.description->' . app()->getLocale() . ' as text')
                    ->selectRaw('null as `data`')
                    ->join('states_models as sm', function ($join) use ($parameter1) {
                        $join->on( 'sm.states_id', 'states.id')
                        ->where('sm.model', $parameter1);
                    });
                $oRecords = $query->clone();
                $oRecords = $oRecords->when(!empty($search), function ($query) use ($search) {
                        return $query->where(function ($query) use ($search) {
                            $query->whereRaw('LOWER(JSON_EXTRACT(states.description, "$.'.app()->getLocale().'")) like ?', ['"%' . strtolower($search) . '%"']);
                        });
                    })
                    ->whereNotIn('states.id', $initOptions)
                    ->orderBy('sm.order', 'asc')
                    ->paginate($maxRecords);

                if (!empty($initOptions)) {
                    $oInitialRecords = $query
                        ->whereIn('states.id', $initOptions)
                        ->orderBy('states.description->' . app()->getLocale(), 'asc')
                        ->paginate($maxRecords);
                }
                break;
            case "treasury_liquidations_types":
                $query = TreasuryLiquidationsType::select('id as value', 'description->' . app()->getLocale() . ' as text')
                    ->selectRaw('null as `data`')
                    ->where('townhalls_id', session('townhall_id'));
                $oRecords = $query->clone();
                $oRecords = $oRecords->when(!empty($search), function ($query) use ($search) {
                        return $query->where(function ($query) use ($search) {
                            $query->whereRaw('LOWER(JSON_EXTRACT(description, "$.'.app()->getLocale().'")) like ?', ['"%' . strtolower($search) . '%"']);
                        });
                    })
                    ->when(!empty($parameter1) && $parameter1=='active', function ($query) {
                        $query->where('treasury_liquidations_types.from_date', '<=', Carbon::now())
                        ->where('treasury_liquidations_types.to_date', '>=', Carbon::now());
                    })
                    ->whereNotIn('id', $initOptions)
                    ->orderBy('description->' . app()->getLocale(), 'asc')
                    ->paginate($maxRecords);

                if (!empty($initOptions)) {
                    $oInitialRecords = $query
                        ->whereIn('id', $initOptions)
                        ->orderBy('description->' . app()->getLocale(), 'asc')
                        ->paginate($maxRecords);
                }
                break;
            case "treasury_procedures":
                $query = TreasuryProcedure::select('id as value', 'description->' . app()->getLocale() . ' as text')
                    ->selectRaw('null as `data`')
                    ->where('townhalls_id', session('townhall_id'));
                $oRecords = $query->clone();
                $oRecords = $oRecords->when(!empty($search), function ($query) use ($search) {
                        return $query->where(function ($query) use ($search) {
                            $query->whereRaw('LOWER(JSON_EXTRACT(description, "$.'.app()->getLocale().'")) like ?', ['"%' . strtolower($search) . '%"']);
                        });
                    })
                    ->when(!empty($parameter1), function ($query) use ($parameter1) {
                        $query->where('.public', $parameter1);
                    })
                    ->whereNotIn('id', $initOptions)
                    ->orderBy('description->' . app()->getLocale(), 'asc')
                    ->paginate($maxRecords);

                if (!empty($initOptions)) {
                    $oInitialRecords = $query
                        ->whereIn('id', $initOptions)
                        ->orderBy('description->' . app()->getLocale(), 'asc')
                        ->paginate($maxRecords);
                }
                break;
            default:
                $options = config('autocomplete.' . $ac_query, []);
                $oRecords = [
                    'total' => length($options),
                    'count' => length($options),
                    'records' => $options,
                    'initial_records' => []
                ];
                break;
        }

        if (is_array($oRecords)) {
            $result = $oRecords;
        } else {
            $result = [
                'total' => $oRecords->total(),
                'count' => length($oRecords->items()),
                'records' => $oRecords->keyBy('value')->toArray(),
                'initial_records' => ($oInitialRecords) ? $oInitialRecords->keyBy('value')->toArray() : [],
            ];
        }

        return $result;
    }
}
