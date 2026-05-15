<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Devlab\LaravelLogs\Traits\WithExtensions;
use App\Traits\HasTranslations;

class TreasuryVaLiquidationsType extends Model
{
    use HasFactory;
    use WithExtensions;
    use HasTranslations;

    public $translatable = ['description'];

}
