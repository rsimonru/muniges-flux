<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactsGroup extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['description'];

    /**
     * Get groups
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = ['contacts']
    ) {

        $oQuery = static::select('contacts_groups.*')
            ->selectRaw('count(distinct cgc.contacts_id) as contacts_number')
            ->leftJoin('contacts_groups_contacts as cgc', 'cgc.groups_id', 'contacts_groups.id')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('contacts_groups.id', $model_id);
            })
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
                return $query->where('contacts_groups.townhalls_id', $filters['townhalls_id']);
            })
            ->when(isset($filters['description']) && ! empty($filters['description']), function ($query) use ($filters) {
                $query->whereRaw('lower(contacts_groups.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['description']).'%"');
            });
        $oQuery->groupBy('contacts_groups.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function delete($do_log = true)
    {

        ContactsGroupsContact::where('groups_id', $this->id)->delete();
        parent::delete();

    }

    public function contacts()
    {
        return $this->hasManyThrough(Contact::class, ContactsGroupsContact::class, 'groups_id', 'id', 'id', 'contacts_id');
    }

    public function contacts_groups_contacts()
    {
        return $this->hasMany(ContactsGroupsContact::class, 'groups_id', 'id');
    }
}
