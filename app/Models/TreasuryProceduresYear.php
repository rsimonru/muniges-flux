<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TreasuryProceduresYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'procedures_id',
        'year',
    ];

}
