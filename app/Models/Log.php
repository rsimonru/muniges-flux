<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Log extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'data' => 'json',
    ];

    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = []
    ) {

        $oQuery = static::select('logs.*')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('logs.id', $model_id);
            })
            ->when(isset($filters['townhall_id']) && ! empty($filters['townhall_id']), function ($query) use ($filters) {
                return $query->where('logs.townhall_id', $filters['townhall_id']);
            })
            ->when(isset($filters['method_id']) && ! empty($filters['method_id']), function ($query) use ($filters) {
                return $query->where('logs.method_id', $filters['method_id']);
            })
            ->when(isset($filters['user_id']) && ! empty($filters['user_id']), function ($query) use ($filters) {
                return $query->where('logs.user_id', $filters['user_id']);
            })
            ->when(isset($filters['data']) && ! empty($filters['data']), function ($query) use ($filters) {
                return $query->whereFullText('data', $filters['data']);
            })
            ->when(isset($filters['date']) && ! empty($filters['date']), function ($query) use ($filters) {
                return $query->whereBetween('logs.'.$filters['date'][0], [$filters['date'][1], $filters['date'][2]]);
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public static function doLog($procedure, $data)
    {
        $user_id = Auth::user()->id ?? config('constants.users.system');
        $method_id = self::checkProcedure($procedure);
        $log = new self;
        $log->method_id = $method_id;
        $log->townhall_id = session('townhall_id', null);
        $log->user_id = $user_id;
        $log->data = $data;
        $log->ip = request()->ip();
        $log->user_agent = request()->userAgent();
        $log->url = request()->url();
        $log->save();
    }

    private static function checkProcedure($procedure)
    {
        $bd_procedure = LogsMethod::where('name', $procedure)->get()->first();
        if (empty($bd_procedure)) {
            $bd_procedure = new LogsMethod;
            $bd_procedure->name = $procedure;
            $bd_procedure->save();
        }

        return $bd_procedure->id;
    }

    public function shows_events()
    {
        return $this->hasMany(ShowsEvent::class, 'shows_id', 'id');
    }
}
