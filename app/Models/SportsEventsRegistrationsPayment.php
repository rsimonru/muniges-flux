<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SportsEventsRegistrationsPayment extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * Get sport events registrations payment
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
        array $with = [],
        array $aggregates = []
    ) {

        $oQuery = static::select('sports_events_registrations_payments.*')
        // ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) {
        //     $query->join('sports_events_registrations as r', 'r.id', 'sports_events_registrations_payments.registrations_id')
        //     ->join('sports_events as e', 'e.id', 'r.events_id')
        //     ->leftJoin('sports_events_registrations_activities as ra','ra.registrations_id', 'r.id')
        //     ->leftJoin('sports_events_activities as a','a.id', 'ra.activities_id')
        //     ->leftJoin('treasury_billing_codes as c','c.id', 'sports_events_registrations_payments.bcodes_id');
        // })
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('sports_events_registrations_payments.id', $model_id);
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->whereHas('sports_events_registration.sports_event', function($query) use ($filters) {
                return $query->where('townhalls_id', $filters['townhalls_id']);
            });
        })
        ->when(isset($filters['payments_ids']) && !empty($filters['payments_ids']), function($query) use ($filters) {
            return $query->whereIn('sports_events_registrations_payments.id', $filters['payments_ids']);
        })
        ->when(isset($filters['events_id']) && !empty($filters['events_id']), function($query) use ($filters) {
            return $query->whereHas('sports_events_registration', function($query) use ($filters) {
                return $query->where('events_id', $filters['events_id']);
            });
        })
        ->when(isset($filters['registrations_id']) && !empty($filters['registrations_id']), function($query) use ($filters) {
            return $query->where('sports_events_registrations_payments.registrations_id', $filters['registrations_id']);
        })
        ->when(isset($filters['registrations_ids']) && !empty($filters['registrations_ids']), function($query) use ($filters) {
            return $query->whereIn('sports_events_registrations_payments.registrations_id', $filters['registrations_ids']);
        })
        ->when(isset($filters['not_registrations_ids']) && !empty($filters['not_registrations_ids']), function($query) use ($filters) {
            return $query->whereNotIn('sports_events_registrations_payments.registrations_id', $filters['not_registrations_ids']);
        })
        ->when(isset($filters['payments_id']) && !empty($filters['payments_id']), function($query) use ($filters) {
            return $query->where('sports_events_registrations_payments.payments_id', $filters['payments_id']);
        })
        ->when(isset($filters['billing_code']) && !empty($filters['billing_code']), function($query) use ($filters) {
            return $query->where('sports_events_registrations_payments.billing_code', $filters['billing_code']);
        })
        ->when(isset($filters['billing_code_search']) && !empty($filters['billing_code_search']), function($query) use ($filters) {
            return $query->where('sports_events_registrations_payments.billing_code', 'like', '%'.$filters['billing_code_search'].'%');
        })
        ->when(isset($filters['states_id']) && !empty($filters['states_id']), function($query) use ($filters) {
            return $query->whereHas('treasury_billing_code', function($query) use ($filters) {
                return $query->where('states_id', $filters['states_id']);
            });
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            $query->where(function($query2) use ($filters) {
                $query2->whereHas('sports_events_registration', function($query) use ($filters) {
                    return $query->whereRaw("concat(sports_events_registrations.name, ' ', sports_events_registrations.surname) like '%".$filters['search']."%'")
                        ->orWhere('sports_events_registrations.vat', 'like', '%'.$filters['search'].'%')
                        ->orWhere('sports_events_registrations.tutor_vat', 'like', '%'.$filters['search'].'%')
                        ->orWhere('sports_events_registrations.tutor_name', 'like', '%'.$filters['search'].'%');
                })
                ->orWhere('sports_events_registrations_payments.billing_code', 'like', '%'.$filters['search'].'%');
            });
        })
        ->when(isset($filters['activities_ids']) && !empty($filters['activities_ids']), function($query) use ($filters) {
             return $query->whereHas('sports_events_registration.sports_events_registrations_activities', function($query) use ($filters) {
                return $query->whereInto('sports_events_registrations_activities.activities_id', $filters['activities_ids'])
                    ->where('sports_events_registrations_activities.states_id', config('states.active'));
            });
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData(oQuery: $oQuery, iModel_id: $model_id, iRecordsInPage:$records_in_page, aWithDerived: $with, aAggregates: $aggregates);
    }

    public function sports_events_registration() {
        return $this->hasOne(SportsEventsRegistration::class, 'id', 'registrations_id')->with('sports_events_registrations_activities','sports_event');
    }
    public function sports_events_payment() {
        return $this->hasOne(SportsEventsPayment::class, 'id', 'payments_id');
    }
    public function treasury_billing_code() {
        return $this->hasOne(TreasuryBillingCode::class, 'id', 'bcodes_id')->with('state');
    }

}
