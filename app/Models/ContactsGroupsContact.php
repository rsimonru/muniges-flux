<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactsGroupsContact extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'groups_id',
        'contacts_id',
    ];

    /**
     * Get contacts-groups contact
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = []
    ) {

        $oQuery = static::select('contacts_groups_contacts.*')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('contacts_groups_contacts.id', $model_id);
            })
            ->when(isset($filters['groups_id']) && ! empty($filters['groups_id']), function ($query) use ($filters) {
                return $query->where('contacts_groups_contacts.groups_id', $filters['groups_id']);
            })
            ->when(isset($filters['contacts_id']) && ! empty($filters['contacts_id']), function ($query) use ($filters) {
                return $query->where('contacts_groups_contacts.contacts_id', $filters['contacts_id']);
            });
        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // dd($oQuery->toSql());
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    /**
     * Overload model save.
     */
    public function save(array $options = [], $do_log = true)
    {
        $contactsgroups = ContactsGroupsContact::where('contacts_id', $this->contacts_id)->where('groups_id', $this->groups_id)->get();
        if (length($contactsgroups) == 0) {
            parent::save($options); // Calls Default Save
        }
    }
}
