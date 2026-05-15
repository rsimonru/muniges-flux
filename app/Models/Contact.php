<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Devlab\LaravelLogs\Traits\WithExtensions;

class Contact extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * Get contacts
     *
     * @param int $model_id
     * @param int $records_in_page
     * @param array $sort (attribute => 'asc'/'desc')
     * @param array $filters
     * @return mixed Collection
     *
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['groups','state']
    ) {

        $oQuery = static::select('contacts.*')
        ->selectRaw('count(distinct cgc.contacts_id) as groups_number')
        ->leftJoin('contacts_groups_contacts as cgc','cgc.contacts_id', 'contacts.id')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('contacts.id', $model_id);
        })
        ->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('contacts.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['states_ids']) && !empty($filters['states_ids']), function($query) use ($filters) {
            return $query->whereIn('contacts.states_id', $filters['states_ids']);
        })
        ->when(isset($filters['groups_ids']) && !empty($filters['groups_ids']), function($query) use ($filters) {
            return $query->whereIn('cgc.groups_id', $filters['groups_ids']);
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            $query->where(function($query2) use ($filters) {
                $query2->whereRaw("concat(contacts.name, ' ', contacts.surname) like '%".$filters['search']."%'")
                ->orWhere('contacts.legal_form', 'like', '%'.$filters['search'].'%')
                ->orWhere('contacts.email', 'like', '%'.$filters['search'].'%');
            });
        })
        ;
        $oQuery->groupBy('contacts.id');

        foreach ($sort as $key => $value) {
            if ($key == 'contact') {
                $oQuery->orderByRaw("concat(contacts.name, contacts.surname, contacts.legal_form) ".$value);
            } else {
                $oQuery->orderBy($key, $value);
            }
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function delete($do_log = true)
    {
        ContactsGroupsContact::where('contacts_id', $this->id)->delete();
        parent::delete();
    }
    public function state()
    {
        return $this->hasOne(State::class, 'id', 'states_id');
    }
    public function groups()
    {
        return $this->hasManyThrough(ContactsGroup::class, ContactsGroupsContact::class, 'contacts_id', 'id', 'id', 'groups_id');
    }
    public function contacts_groups_contacts()
    {
        return $this->hasMany(ContactsGroupsContact::class, 'contacts_id', 'id');
    }
}
