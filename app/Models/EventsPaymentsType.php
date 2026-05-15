<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class EventsPaymentsType extends Model
{
    use HasFactory;
    use HasTranslations;

    public $translatable = ['description'];

}
