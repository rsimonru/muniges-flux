<?php

namespace App\Models;

use Devlab\LaravelLogs\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class TreasuryProcedure extends Model
{
    use HasFactory;
    use WithExtensions;
    use HasTranslations;

    public $translatable = ['description'];

    protected $fillable = [
        'code',
        'description',
        'townhalls_id',
        'public',
    ];

    protected $casts = [
        'public' => 'boolean',
    ];

    /**
     * Get bank accounts
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

        if (!isset($filters['year'])) {
            $filters['year'] = date("Y");
        }
        $oQuery = static::select('treasury_procedures.*')
        ->join('treasury_procedures_years as y',  'treasury_procedures.id', 'y.procedures_id')
        ->where('y.year', $filters['year'])
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('treasury_procedures.id', $model_id);
        });

        $oQuery = static::dlApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }
    public static function dlApplyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['townhalls_id']) && !empty($filters['townhalls_id']), function($query) use ($filters) {
            return $query->where('treasury_procedures.townhalls_id', $filters['townhalls_id']);
        })
        ->when(isset($filters['description'])  && !empty($filters['description']), function($query) use ($filters) {
            return $query->whereRaw('lower(treasury_procedures.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['description']).'%"');
        })
        ->when(isset($filters['public']), function($query) use ($filters) {
            return $query->where('treasury_procedures.public', $filters['public']);
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            return $query->where(function ($query2) use ($filters) {
                $query2->whereRaw('lower(treasury_procedures.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['search']).'%"')
                ->orWhere('y.code', 'like', '%'.$filters['search'].'%')
                ;
            });

        });

        return $oQuery;
    }

    public function procedure_year() {
        return $this->hasOne(TreasuryProceduresYear::class, 'procedures_id', 'id')
            ->where('treasury_procedures_years.year',date("Y"));
    }

}
