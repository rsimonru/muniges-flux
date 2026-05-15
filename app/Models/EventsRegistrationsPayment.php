<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventsRegistrationsPayment extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * Get events registrations payment
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

        $oQuery = static::select('events_registrations_payments.*')
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) {
            $query->join('events_registrations as r', 'r.id', 'events_registrations_payments.registrations_id')
            ->join('events as e', 'e.id', 'r.events_id')
            ->leftJoin('events_registrations_activities as ra','ra.registrations_id', 'r.id')
            ->leftJoin('events_activities as a','a.id', 'ra.activities_id')
            ->leftJoin('treasury_billing_codes as c','c.id', 'events_registrations_payments.bcodes_id');
        })
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('events_registrations_payments.id', $model_id);
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('e.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['payments_ids']) && !empty($filters['payments_ids']), function($query) use ($filters) {
            return $query->whereIn('events_registrations_payments.id', $filters['payments_ids']);
        })
        ->when(isset($filters['events_id']) && !empty($filters['events_id']), function($query) use ($filters) {
            return $query->where('r.events_id', $filters['events_id']);
        })
        ->when(isset($filters['registrations_id']) && !empty($filters['registrations_id']), function($query) use ($filters) {
            return $query->where('events_registrations_payments.registrations_id', $filters['registrations_id']);
        })
        ->when(isset($filters['registrations_ids']) && !empty($filters['registrations_ids']), function($query) use ($filters) {
            return $query->whereIn('events_registrations_payments.registrations_id', $filters['registrations_ids']);
        })
        ->when(isset($filters['not_registrations_ids']) && !empty($filters['not_registrations_ids']), function($query) use ($filters) {
            return $query->whereNotIn('events_registrations_payments.registrations_id', $filters['not_registrations_ids']);
        })
        ->when(isset($filters['payments_id']) && !empty($filters['payments_id']), function($query) use ($filters) {
            return $query->where('events_registrations_payments.payments_id', $filters['payments_id']);
        })
        ->when(isset($filters['billing_code']) && !empty($filters['billing_code']), function($query) use ($filters) {
            return $query->where('events_registrations_payments.billing_code', $filters['billing_code']);
        })
        ->when(isset($filters['billing_code_search']) && !empty($filters['billing_code_search']), function($query) use ($filters) {
            return $query->where('events_registrations_payments.billing_code', 'like', '%'.$filters['billing_code_search'].'%');
        })
        ->when(isset($filters['states_id']) && !empty($filters['states_id']), function($query) use ($filters) {
            return $query->where('c.states_id', $filters['states_id']);
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            $query->where(function($query2) use ($filters) {
                $query2->whereRaw("concat(r.name, ' ', r.surname) like '%".$filters['search']."%'")
                ->orWhere('r.vat', 'like', '%'.$filters['search'].'%')
                ->orWhere('r.tutor_vat', 'like', '%'.$filters['search'].'%')
                ->orWhere('r.tutor_name', 'like', '%'.$filters['search'].'%')
                ->orWhere('events_registrations_payments.billing_code', 'like', '%'.$filters['search'].'%');
            });
        })
        ->when(isset($filters['activities_ids']) && !empty($filters['activities_ids']), function($query) use ($filters) {
            return $query->whereIn('ra.activities_id', $filters['activities_ids'])
            ->where('ra.states_id', config('states.active'));
        })
        ;
        $oQuery->groupBy('events_registrations_payments.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function events_registration() {
        return $this->hasOne(EventsRegistration::class, 'id', 'registrations_id')->with('events_registrations_activities','event');
    }
    public function events_payment() {
        return $this->hasOne(EventsPayment::class, 'id', 'payments_id');
    }
    public function treasury_billing_code() {
        return $this->hasOne(TreasuryBillingCode::class, 'id', 'bcodes_id')->with('state');
    }

}
