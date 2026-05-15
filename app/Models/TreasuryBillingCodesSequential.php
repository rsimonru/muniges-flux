<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class TreasuryBillingCodesSequential extends Model
{
    use HasFactory;


    /**
     * Get billing code
     *
     * @param int $townhalls_id
     * @return string code
     *
     */
    public static function getCode(
        int $townhalls_id
    ) {
        $letters='TRWAGMYFPDXBNJZSQVHLCKE';
        $code = '';
        if (!empty($townhalls_id)) {
            $sequential = 0;
            DB::transaction(function () use (&$sequential) {
                $tbc_sequential = TreasuryBillingCodesSequential::find(session('townhall_id'));
                $sequential = $tbc_sequential->sequential;
                $tbc_sequential->sequential++;
                $tbc_sequential->save([],false);
            }, 5);

            $number = ($sequential % 1000000) % 23;
            $code = date("y").($sequential % 1000000).substr($letters,$number,1);
        }

        return $code;
    }

}
