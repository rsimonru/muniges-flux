<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class TownHall extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['name', 'short_name', 'lopd_text', 'payments_text'];

    protected $fillable = [
        'name',
        'short_name',
        'address',
        'town',
        'province',
        'zip',
        'phone',
        'vat',
        'email',
        'from_email',
        'web',
        'payletter_templates_id',
        'payproof_templates_id',
        'selfliq_templates_id',
        'propliq_templates_id',
        'lopd_text',
        'payments_text',
    ];

    /**
     * Get town halls
     *
     * @param  array  $sort  (attribute => 'asc'/'desc')
     * @return mixed Collection
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = []
    ) {

        $oQuery = static::select('town_halls.*')
            ->when($model_id > 0, function ($query) use ($model_id) {
                return $query->where('town_halls.id', $model_id);
            })
            ->when(isset($filters['zip']) && ! empty($filters['zip']), function ($query) use ($filters) {
                $query->where('town_halls.zip', $filters['zip']);
            })
            ->when(isset($filters['name']) && ! empty($filters['name']), function ($query) use ($filters) {
                $query->whereRaw('lower(town_halls.name->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($filters['name']).'%"');
            });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function save(array $options = [], $do_log = true)
    {
        $bNew = ($this->id > 0) ? false : true;

        parent::save($options); // Calls Default Save

        if ($bNew) {
            $townhall_url = new TownHallsUrl;
            $townhall_url->townhalls_id = $this->id;
            $townhall_url->type = 'intranet';
            $townhall_url->url = $this->url_prefix.'-int.muniges.es';
            $townhall_url->order = 1;
            $townhall_url->save();
            $townhall_url = new TownHallsUrl;
            $townhall_url->townhalls_id = $this->id;
            $townhall_url->type = 'sac';
            $townhall_url->url = $this->url_prefix.'.muniges.es';
            $townhall_url->order = 1;
            $townhall_url->save();
            $townhall_url = new TownHallsUrl;
            $townhall_url->townhalls_id = $this->id;
            $townhall_url->type = 'app';
            $townhall_url->url = $this->url_prefix.'-app.muniges.es';
            $townhall_url->order = 1;
            $townhall_url->save();

            $user = new User;
            $user->name = 'Ciudadano';
            $user->email = 'muniges-'.$this->id.'@muniges.es';
            $user->password = '_SinClave_';
            $user->active = 1;
            $user->save();

            $user_townhalls = new UsersTownHall;
            $user_townhalls->users_id = $user->id;
            $user_townhalls->townhalls_id = $this->id;
            $user_townhalls->roles_id = 1;
            $user_townhalls->save();

            $townhall_lang = new TownHallsLang;
            $townhall_lang->townhalls_id = $this->id;
            $townhall_lang->lang = 'es';
            $townhall_lang->save();

            $sequential = new TreasuryBillingCodesSequential;
            $sequential->townhalls_id = $this->id;
            $sequential->sequential = 101;
            $sequential->save();

            $path = 'aytos/'.$this->id.'/cert';
            if (! Storage::exists($path)) {
                Storage::makeDirectory($path, 0775, true);
            }
        }

    }

    public function town_halls_urls()
    {
        return $this->hasMany(TownHallsUrl::class, 'townhalls_id', 'id')->orderBy('order', 'asc')->orderBy('type', 'asc');
    }

    public function town_halls_langs()
    {
        return $this->hasMany(TownHallsLang::class, 'townhalls_id', 'id')->orderBy('lang', 'asc');
    }

    public function users()
    {
        return $this->hasMany(UsersTownHall::class, 'townhalls_id', 'id')->with('user');
    }
}
