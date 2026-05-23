<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Download extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithExtensions;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'starts_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'filters' => 'json',
    ];

    /**
     * Get customers.
     *
     * @param  int  $user_id
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        ?int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        ?array $filters = [],
        array $with = []
    ) {

        $model_id = ($model_id) ? $model_id : 0;

        $oQuery = static::select('downloads.*');

        $oQuery->when($model_id > 0, function ($query) use ($model_id) {
            return $query->where('downloads.id', $model_id);
        })
            ->when(isset($filters['models_ids']) && ! empty($filters['models_ids']), function ($query) use ($filters) {
                return $query->whereIn(static::getTableName().'.id', $filters['models_ids']);
            })
            ->when(isset($filters['user_id']) && ! empty($filters['user_id']), function ($query) use ($filters) {
                return $query->where('downloads.user_id', $filters['user_id']);
            })
            ->when(isset($filters['downloaded']) && $filters['downloaded'] == false, function ($query) {
                return $query->whereNull('downloads.downloaded_at');
            })
            ->when(isset($filters['file_name']) && $filters['file_name'] == false, function ($query) use ($filters) {
                return $query->where('downloads.file_name', 'like', '%'.$filters['file_name'].'%');
            })
            ->when(isset($filters['finished']) && $filters['finished'] == true, function ($query) {
                return $query->whereNotNull('downloads.finished_at');
            })
            ->when(isset($filters['search']) && ! empty($filters['search']), function ($query) use ($filters) {
                return $query->where(function ($query) use ($filters) {
                    $query->where('downloads.id', $filters['search'])
                        ->orWhere('downloads.file_name', 'like', '%'.$filters['search'].'%');
                });
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function delete($do_log = true)
    {
        Storage::delete($this->path);
        parent::delete();
    }
}
