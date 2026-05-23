<?php

namespace App\Traits;

use App\Classes\emtSign;
use Illuminate\Database\Eloquent\Builder;

trait WithExtensions
{
    // public function getTokenAttribute() {
    //     return emtSign::sign($this->id);
    // }

    public function scopeWhereInto(Builder $query, $field, $value)
    {
        if (is_array($value)) {
            return $query->whereIn($field, $value);
        } else {
            $value = explode(',', $value);

            return $query->whereIn($field, $value);
        }
    }

    public function scopeWhereNotInto(Builder $query, $field, $value)
    {
        if (is_array($value)) {
            return $query->whereNotIn($field, $value);
        } else {
            $value = explode(',', $value);

            return $query->whereNotIn($field, $value);
        }
    }

    public function scopeOrWhereInto(Builder $query, $field, $value)
    {
        if (is_array($value)) {
            return $query->orWhereIn($field, $value);
        } else {
            $value = explode(',', $value);

            return $query->orWhereIn($field, $value);
        }
    }

    public function scopeOrWhereNotInto(Builder $query, $field, $value)
    {
        if (is_array($value)) {
            return $query->orWhereNotIn($field, $value);
        } else {
            $value = explode(',', $value);

            return $query->orWhereNotIn($field, $value);
        }
    }

    public static function getModelData($oQuery, $iModel_id, $iRecordsInPage = 0, $aWithDerived = [], $keyBy = 'id', $pageNumber = null, $aAggregates = [], $aCounts = [])
    {

        if (! empty($aWithDerived)) {
            $oQuery->with($aWithDerived);
        }
        if (! empty($aAggregates)) {
            $oQuery->withAggregate($aAggregates[0], $aAggregates[1]);
        }
        if (! empty($aCounts)) {
            $oQuery->withCount($aCounts);
        }
        if ($iModel_id == 0) {
            // $iRecordsInPage = ($iRecordsInPage <= 0 || empty($iRecordsInPage)) ? config('constants.pagination.DEFAULT_PAGE_RECORDS') : $iRecordsInPage;
            $iRecordsInPage = ($iRecordsInPage == 0) ? config('constants.pagination.DEFAULT_PAGE_RECORDS') : $iRecordsInPage;
            if ($iRecordsInPage > 0) {
                $oRecords = $oQuery->paginate($iRecordsInPage, ['*'], 'page', $pageNumber)->withQueryString();
                $oRecordsC = $keyBy ? $oRecords->getCollection()->keyBy($keyBy) : $oRecords->getCollection();
                $oRecords->setCollection($oRecordsC);
            } else {
                $oRecords = $keyBy ? $oQuery->get()->keyBy($keyBy) : $oQuery->get();
            }
        } else {
            $oRecords = $oQuery->get()->first();
        }

        return $oRecords;
    }
}
