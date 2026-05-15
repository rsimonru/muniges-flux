<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SportsInstallationsReservation extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
        'expiration_date' => 'datetime',
    ];

    /**
     * Get sport installations reservations
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
        array $with = ['sports_installations_resource','state','treasury_billing_code']
    ) {

        $oQuery = static::select('sports_installations_reservations.*')
        ->join('sports_installations_resources as r','r.id','sports_installations_reservations.resources_id')
        ->join('sports_installations_resources_groups as g','g.id','r.groups_id')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('sports_installations_reservations.id', $model_id);
        })
        ->when(isset($filters['resources_id']) && !empty($filters['resources_id']), function($query) use ($filters) {
            return $query->where('sports_installations_reservations.resources_id', $filters['resources_id']);
        })
        ->when(isset($filters['groups_id']) && !empty($filters['groups_id']), function($query) use ($filters) {
            return $query->where('r.groups_id', $filters['groups_id']);
        })
        ->when(isset($filters['states_id']) && !empty($filters['states_id']), function($query) use ($filters) {
            return $query->where('sports_installations_reservations.states_id', $filters['states_id']);
        })
        ->when(isset($filters['states_ids']) && !empty($filters['states_ids']), function($query) use ($filters) {
            return $query->whereIn('sports_installations_reservations.states_id', $filters['states_ids']);
        })
        ->when(isset($filters['installations_id']) && !empty($filters['installations_id']), function($query) use ($filters) {
            return $query->where('g.installations_id', $filters['installations_id']);
        })
        ->when(isset($filters['name']) && !empty($filters['name']), function($query) use ($filters) {
            $query->whereRaw('lower(sports_installations_reservations.name->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['name']).'%"');
        })
        ->when(isset($filters['expiration_date']) && !empty($filters['expiration_date']), function($query) use ($filters) {
            return $query->whereBetween('sports_installations_reservations.expiration_date', $filters['expiration_date']);
        })
        ->when(isset($filters['date']) && !empty($filters['date']), function ($query) use ($filters) {
            return $query->where(function ($query) use ($filters){
                $query->whereBetween('sports_installations_reservations.from_date', $filters['date'])
                    ->orWhereBetween('sports_installations_reservations.to_date', $filters['date']);
            });
        })
        ->when(isset($filters['day']) && !empty($filters['day']), function ($query) use ($filters) {
            $day_end = $filters['day']->clone()->endOfDay();
            $query->whereBetween('sports_installations_reservations.from_date',  [$filters['day'],$day_end]);
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function save($params = array(), $options = array(), $do_log = true)
    {
        $new = false;
        if ($this->id>0) {
            parent::save($options); // Calls Default Save
            return $this->id;
        } else {
            $new = true;
            $reservations = [];
            $group = null;
            $resources = SportsInstallationsResource::emtGetAvailable([],[
                // 'from_date' => $this->from_date,
                // 'to_date' => $this->to_date,
                'day' => $this->from_date,
                'rented' => $this->rented,
                'groups_id' => $params['groups_id'],
            ]);
            $resources_count = length($resources);
            if ($resources_count>0) {
                $reservations = static::emtGet(0,-1,[],[
                    'groups_id' => $params['groups_id'],
                    'date' => [$this->from_date, $this->to_date],
                    'states_ids' => [config('states.pending'), config('states.rented'), config('states.reserved')],
                ]);
                $tickets_count = 0;
                $tickets_vat_count = 0;
                foreach ($reservations as $reservation) {
                    $tickets_count += $reservation->tickets + $reservation->tickets2;
                    if ($reservation->vat == $this->vat) {
                        $tickets_vat_count += $reservation->tickets + $reservation->tickets2;
                    }
                }
                $group = $resources->first()->sports_installations_resources_group;
                if ($group->capacity == 1) {
                    // Get free resource for the group
                    foreach ($reservations as $key => $reservation) {
                        if (isset($resources[$reservation->resources_id])) {
                            $resources = $resources->filter(function($item) use ($reservation) {
                                return $item->id != $reservation->resources_id;
                            });
                        }
                    }
                    if (length($resources)==0) {
                        return -1; // There are no resources
                    }
                } else {
                    if (($tickets_count + $reservation->tickets + $reservation->tickets2) > $group->capacity) {
                        return -2; // Out of capacity
                    }
                    if (($tickets_vat_count + $reservation->tickets + $reservation->tickets2) > $group->max_tickets) {
                        return -3; // Max tickets reached
                    }
                }
            } else return -1; // There are no resources

            $resource = $resources->first();
            $this->resources_id = $resource->id;
            $this->billing_code = '';
            parent::save($options); // Calls Default Save
            if ($new && $this->rented && $params['amount']>0) {
                // New billing code
                $bcode = new TreasuryBillingCode();
                $bcode->townhalls_id = session('townhall_id');
                $bcode->vat = $this->vat;
                $bcode->passport = 0;
                $bcode->thirdparty = $this->name;
                $bcode->procedures_id = $group->sports_installation->procedures_id;
                $bcode->model = get_class($this);
                $bcode->models_id = $this->id;
                $bcode->liquidation = $this->id;
                $bcode->record = '';
                $bcode->observations = trans_choice('sports.reservations',1).' '.$group->sports_installation->name.' - '.$resource->name.
                    ' ('.$this->from_date->format('d/m/Y H:i').' - '.$this->to_date->format('H:i').')'.
                    (($group->slot<0) ? ' - '.__('sports.tickets').': '.$this->tickets .', '.__('sports.reduced_tickets').': '.$this->tickets2:'');
                $bcode->states_id = config('states.pending');
                $bcode->amount = $params['amount'];
                $bcode->save();

                $this->billing_code = $bcode->code;
                parent::save($options); // Calls Default Save
            }
            return $this->id;
        }
    }

    public function sports_installations_resource() {
        return $this->hasOne(SportsInstallationsResource::class, 'id', 'resources_id')->with('sports_installations_resources_group');
    }
    public function state() {
        return $this->hasOne(State::class, 'id', 'states_id');
    }
    public function treasury_billing_code() {
        return $this->hasOne(TreasuryBillingCode::class, 'code', 'billing_code')
        ->where('treasury_billing_codes.townhalls_id',session('townhall_id'))
        ->with('state');
    }

}
