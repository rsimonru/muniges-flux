<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Permission extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['description'];

    /**
     * Get permissions
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

        $oQuery = static::select('permissions.*')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('permissions.id', $model_id);
            })
            ->where('permissions.level', '<=', Auth::user()->level->level);

        $oQuery = static::dlApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public static function dlApplyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['class']) && ! empty($filters['class']), function ($query) use ($filters) {
            return $query->where('permissions.class', $filters['class']);
        })
            ->when(isset($filters['model']) && ! empty($filters['model']), function ($query) use ($filters) {
                return $query->where('permissions.model', $filters['model']);
            })
            ->when(isset($filters['model_id']) && ! empty($filters['model_id']), function ($query) use ($filters) {
                return $query->where('permissions.model_id', $filters['model_id']);
            })
            ->when(isset($filters['description']) && ! empty($filters['description']), function ($query) use ($filters) {
                $query->whereRaw('lower(permissions.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['description']).'%"');
            });

        return $oQuery;
    }

    public function menu()
    {
        return $this->hasOne(Menu::class, 'id', 'model_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, ModelHasPermission::class, 'permission_id', 'model_id', 'id', 'id');
    }

    public function groups()
    {
        return $this->belongsToMany(Role::class, RoleHasPermission::class, 'permission_id', 'role_id', 'id', 'id');
    }
}
