<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as LaravelModel;
use App\Classes\Panel;

class Model extends LaravelModel
{

    /**
	 * Overload model save.
	 */
    public function save (array $options = array(), $do_log = true)
    {
        $user_id = auth()->user()->id ?? 0;
        if ($user_id==0) { // There are no login
            if (empty($this->id)) {
                if (array_key_exists('created_user', $this->attributes) && !empty($this->created_user)) {
                    $user_id = $this->created_user;
                }
            } else {
                if (array_key_exists('updated_user', $this->attributes) && !empty($this->updated_user)) {
                    $user_id = $this->updated_user;
                }
            }
        } else {
            if (empty($this->id)) {
                if (array_key_exists('created_user', $this->attributes)) {
                    $this->created_user = $user_id;
                }
            } else {
                if (array_key_exists('updated_user', $this->attributes)) {
                    $this->updated_user = $user_id;
                }
            }
        }
        if ($do_log) {
            Panel::saveLog(get_class($this).'::save', [
                'original' => $this->getOriginal(),
                'changes' => $this->getDirty(),
            ]);
        }
        parent::save($options); // Calls Default Save
    }
    /**
	 * Masive insert.
	 */
    public static function emtInsert (array $records = array(), $do_log = true) {

        if ($do_log) {
            Panel::saveLog(static::class.'::emtInsert', [
                'records' => $records,
            ]);
        }

        static::insert($records); // Calls Default Save
    }
    /**
	 * Masive delete.
	 */
    public static function emtDelete (array $ids = [], $do_log = true) {

        if ($do_log) {
            Panel::saveLog(static::class.'::emtDelete', [
                'ids' => $ids,
            ]);
        }

        static::whereIn('id', $ids)->delete();
    }
    /**
	 * Overload model delete.
	 */
    public function delete ($do_log = true)
    {
        if ($do_log) {
            Panel::saveLog(get_class($this).'::delete', [
                'id' => $this->id
            ]);
        }

        if (array_key_exists('updated_user', $this->attributes) && empty($this->updated_user)) {
            $this->updated_user = auth()->user()->id ?? $this->updated_user;
        }

        parent::delete(); // Calls Default Save
    }

}
