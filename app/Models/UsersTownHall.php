<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersTownHall extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function user() {
        return $this->hasOne(User::class, 'id', 'users_id');
    }
    public function town_hall() {
        return $this->hasOne(TownHall::class, 'id', 'townhalls_id');
    }
    public function level() {
        return $this->hasOne(Level::class, 'id', 'level_id');
    }
}
