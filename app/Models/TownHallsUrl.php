<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TownHallsUrl extends Model
{
    use HasFactory;

    public function townhall() {
        return $this->hasOne(TownHall::class, 'id', 'townhalls_id');
    }
}
