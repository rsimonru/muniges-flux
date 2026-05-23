<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['title', 'text'];

    protected $casts = [
        'send_at' => 'datetime',
    ];

    /**
     * Get notifications
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

        $oQuery = static::select('notifications.*')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('notifications.id', $model_id);
            })
            ->when(isset($filters['townhalls_id']) && ! empty($filters['townhalls_id']), function ($query) use ($filters) {
                return $query->where('notifications.townhalls_id', $filters['townhalls_id']);
            })
            ->when(isset($filters['title']) && ! empty($filters['title']), function ($query) use ($filters) {
                $query->whereRaw('lower(notifications.title->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['title']).'%"');
            })
            ->when(isset($filters['send_at']) && ! empty($filters['send_at']), function ($query) use ($filters) {
                return $query->where(function ($query) use ($filters) {
                    $query->whereBetween('notifications.send_at', $filters['send_at']);
                });
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }
}
