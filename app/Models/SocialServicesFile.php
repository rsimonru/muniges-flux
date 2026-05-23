<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialServicesFile extends Model
{
    use HasFactory;
    use WithExtensions;

    /**
     * Get social services files
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

        $oQuery = static::select('social_services_files.*')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('social_services_files.id', $model_id);
            })
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
                return $query->where('social_services_files.townhalls_id', $filters['townhalls_id']);
            })
            ->when(isset($filters['search']) && ! empty($filters['search']), function ($query) use ($filters) {
                $query->where(function ($query2) use ($filters) {
                    $query2->whereRaw("concat(social_services_files.name, ' ', social_services_files.surname) like '%".$filters['search']."%'")
                        ->orWhere('social_services_files.file_number', 'like', '%'.$filters['search'].'%');
                });

            })
            ->when(isset($filters['created_at']) && ! empty($filters['created_at']), function ($query) use ($filters) {
                return $query->whereBetween('social_services_files.created_at', $filters['created_at']);
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }
}
