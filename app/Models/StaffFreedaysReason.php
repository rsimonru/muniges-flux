<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class StaffFreedaysReason extends Model
{
    use HasFactory;
    use HasTranslations;

    public $translatable = ['description'];

    public function color() {
        return $this->hasOne(Color::class, 'id', 'colors_id');
    }

}
