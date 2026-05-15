<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use App\Models\Role;
use App\Models\Color;
use App\Models\Province;

class Select extends Model
{

    public static function get_day($d) {
        $aDays = [
            1 => ['es' => 'Lunes', 'en' => 'Monday'],
            2 => ['es' => 'Martes', 'en' => 'Tuesday'],
            3 => ['es' => 'Miércoles', 'en' => 'Wednesday'],
            4 => ['es' => 'Jueves', 'en' => 'Thursday'],
            5 => ['es' => 'Viernes', 'en' => 'Friday'],
            6 => ['es' => 'Sábado', 'en' => 'Saturday'],
            7 => ['es' => 'Domingo', 'en' => 'Sunday'],
        ];
        return ['value' => $d, "option" => $aDays[$d][app()->getLocale()]];
    }

    /**
     * Get select.
     *
     * @param string $vcSelect
     * @param string $vcParameter1
     * @param string $vcParameter2
     * @param string $vcParameter3
     * @param string $vcParameter4
     * @return mixed Colletion
     *
     */
    public static function emtGet(
        string $vcSelect,
        $vcParameter1 = '',
        $vcParameter2 = '',
        $vcParameter3 = '',
        $vcParameter4 = ''
    ) {

        $oSelect = null;

        switch ($vcSelect) {
            case "colors":
                $oSelect = Color::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->orderBy('description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "contacts_groups":
                $oSelect = ContactsGroup::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->where('townhalls_id', session('townhall_id'))
                    ->orderBy('description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "events_payments_types":
                $oSelect = EventsPaymentsType::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->orderBy('events_payments_types.description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "events":
                $oSelect = Event::select('id as value', 'name->'.app()->getLocale().' as option')
                    ->where('events.townhalls_id', session('townhall_id'))
                    ->orderBy('events.from_date', 'desc')
                    ->get()->toArray();
                break;
            case "events_activities":
                $oSelect = EventsActivity::select('id as value', 'name->'.app()->getLocale().' as option')
                    ->where('events_activities.events_id', $vcParameter1)
                    ->orderBy('events_activities.name->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "events_payments":
                $oSelect = EventsPayment::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->where('events_payments.events_id', $vcParameter1)
                    ->orderBy('events_payments.description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "forms_areas":
                $oSelect = FormsArea::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->orderBy('description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "helps_url_types":
                $oSelect = HelpsUrlType::select('id as value', 'description as option')
                    ->orderBy('description', 'asc')
                    ->get()->toArray();
                break;
            case "levels":
                $oSelect = Level::select('id as value', 'name->'.app()->getLocale().' as option')
                    ->when(!empty($vcParameter1), function ($query) use ($vcParameter1) {
                        $query->where('level', '<=', $vcParameter1);
                    })
                    ->orderBy('level', 'asc')
                    ->get()->toArray();
                break;
            case "months":
                $oSelect = [
                    ["value" => 1, "option" => "Enero"], ["value" => 2, "option" => "Febrero"], ["value" => 3, "option" => "Marzo"],
                    ["value" => 4, "option" => "Abril"], ["value" => 5, "option" => "Mayo"], ["value" => 6, "option" => "Junio"],
                    ["value" => 7, "option" => "Julio"], ["value" => 8, "option" => "Agosto"], ["value" => 9, "option" => "Septiembre"],
                    ["value" => 10, "option" => "Octubre"], ["value" => 11, "option" => "Noviembre"], ["value" => 12, "option" => "Diciembre"]
                ];
                break;
            case "provinces":
                $oSelect = Province::select('id as value', 'province as option')
                    ->orderBy('id', 'asc')
                    ->get()->toArray();
                break;
            case "schedules":
                $oSelect = Schedule::select('schedules.id as value', 'schedules.description->'.app()->getLocale().' as option')
                    ->leftjoin('users_schedules as us', 'us.schedules_id', 'schedules.id')
                    ->leftjoin('groups_schedules as gs', 'gs.schedules_id', 'schedules.id')
                    ->leftjoin('users_groups as ug','ug.groups_id','gs.groups_id')
                    ->where(function ($query2) use ($vcParameter1) {
                        return $query2->where('us.users_id', $vcParameter1)
                            ->orWhere('ug.users_id', $vcParameter1);
                    })
                    ->where('schedules.townhalls_id', session('townhall_id'))
                    ->distinct()
                    ->orderBy('schedules.description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "services_categories":
                $oSelect = ServicesCategory::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->orderBy('description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "services_events_assigned_users":
                $oSelect = User::select('users.id as value', 'users.name as option')
                    ->join('services_events_notes as sen', function ($join) use ($vcParameter1) {
                        $join->on('sen.assigned_user_id', 'users.id')
                        //->where('sm.model', $vcParameter1)
                        ;
                    })
                    ->join('services_events as se', function ($join) use ($vcParameter1) {
                        $join->on('sen.events_id', 'se.id')
                        //->where('sm.model', $vcParameter1)
                        ;
                    })
                    ->where('se.townhalls_id', session('townhall_id'))
                    ->groupBy('users.id')
                    ->orderBy('users.name', 'asc')
                    ->get()->toArray();
                break;
            case "shows":
                $oSelect = Show::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->where('townhalls_id', session('townhall_id'))
                    ->when(!empty($vcParameter1), function ($query) {
                        $query->where('from_date', '<=', today())
                            ->where('to_date', '>=', today());
                    })
                    ->when(!empty($vcParameter2), function ($query) {
                        $query->where(function ($query) {
                            $query->whereExists(function ($query2) {
                                $query2->selectRaw(1)
                                ->from('tickets_users_permissions as p')
                                ->whereColumn('p.model_id','shows.id')
                                ->where('p.model', Show::class)
                                ->where('p.users_id', auth()->user()->id);
                            });
                        });
                    })
                    ->orderBy('from_date', 'desc')
                    ->get()->toArray();
                break;
            case "shows_events":
                $oSelect = ShowsEvent::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->where('shows_id', $vcParameter1)
                    ->when(!empty($vcParameter2), function ($query) {
                        $query->where('tickets_to_date', '>', now());
                    })
                    ->when(!empty($vcParameter3), function ($query) {
                        $query->where('to_date', '>=', today());
                    })
                    ->when(!empty($vcParameter4), function ($query) {
                        $query->where('tickets_from_date', '<', now());
                    })
                    ->orderBy('description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "shows_events_sessions":
                $oSelect = ShowsEventsSession::select('id as value')
                    ->selectRaw("date_format(`date`, '%d/%m/%Y %H:%i') as `option`")
                    ->where('events_id', $vcParameter1)
                    ->when(!empty($vcParameter2), function ($query) {
                        $query->where('date', '>', now());
                    })
                    ->orderBy('date', 'asc')
                    ->get()->toArray();
                break;
            case "shows_rooms":
                $oSelect = ShowsRoom::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->where('townhalls_id', session('townhall_id'))
                    ->where('active', 1)
                    ->orderBy('description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "shows_tickets_types":
                $oSelect = ShowsTicketsType::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->orderBy('description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "sports_events_payments_types":
                $oSelect = SportsEventsPaymentsType::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->orderBy('sports_events_payments_types.description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "sports_events":
                $oSelect = SportsEvent::select('id as value', 'name->'.app()->getLocale().' as option')
                    ->where('sports_events.townhalls_id', session('townhall_id'))
                    ->orderBy('sports_events.from_date', 'desc')
                    ->get()->toArray();
                break;
            case "sports_events_activities":
                $oSelect = SportsEventsActivity::select('id as value', 'name->'.app()->getLocale().' as option')
                    ->where('sports_events_activities.events_id', $vcParameter1)
                    ->orderBy('sports_events_activities.name->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "sports_events_payments":
                $oSelect = SportsEventsPayment::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->where('sports_events_payments.events_id', $vcParameter1)
                    ->orderBy('sports_events_payments.description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "sports_installations":
                $oSelect = SportsInstallation::select('id as value', 'name->'.app()->getLocale().' as option')
                    ->where('townhalls_id', session('townhall_id'))
                    ->when(!empty($vcParameter2), function ($query) {
                        $query->where(function ($query) {
                            $query->whereExists(function ($query2) {
                                $query2->selectRaw(1)
                                ->from('tickets_users_permissions as p')
                                ->whereColumn('p.model_id','sports_installations.id')
                                ->where('p.model', SportsInstallation::class)
                                ->where('p.users_id', auth()->user()->id);
                            });
                        });
                    })
                    ->orderBy('name->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "sports_installations_resources_groups":
                $oSelect = SportsInstallationsResourcesGroup::select('id as value', 'name->'.app()->getLocale().' as option')
                    //->where('townhalls_id', session('townhall_id'))
                    ->where('sports_installations_resources_groups.installations_id', $vcParameter1)
                    ->orderBy('name->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "sports_installations_groups_slots":
                $oSelect = SportsInstallationsGroupsSlot::select('value as value', 'description->'.app()->getLocale().' as option')
                    ->orderBy('id', 'asc')
                    ->get()->toArray();
                break;
            case "sports_installations_icons":
                $oSelect = [
                    ["value" => "fas fa-basketball-ball", "option" => '<i class="fas fa-lg fa-basketball-ball"></i>'],
                    ["value" => "fas fa-table-tennis", "option" => '<i class="fas fa-lg fa-table-tennis"></i>'],
                    ["value" => "fas fa-baseball-ball", "option" => '<i class="fas fa-lg fa-baseball-ball"></i>'],
                    ["value" => "fas fa-futbol", "option" => '<i class="fas fa-lg fa-futbol"></i>'],
                    ["value" => "fas fa-swimmer", "option" => '<i class="fas fa-lg fa-swimmer"></i>'],
                ];
                break;
            case "staff_areas":
                $oSelect = StaffArea::select('staff_areas.id as value', 'staff_areas.description->'.app()->getLocale().' as option')
                    ->where('staff_areas.townhalls_id', session('townhall_id'))
                    ->orderBy('staff_areas.description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "staff_employees":
                $oSelect = StaffEmployee::select('staff_employees.id as value')
                    ->selectRaw("concat(staff_employees.surname,', ',staff_employees.name) as `option`")
                    ->where('staff_employees.townhalls_id', session('townhall_id'))
                    ->orderBy('staff_employees.surname', 'asc')
                    ->get()->toArray();
                break;
            case "staff_freedays_reasons":
                $oSelect = StaffFreedaysReason::select('staff_freedays_reasons.id as value', 'staff_freedays_reasons.description->'.app()->getLocale().' as option')
                    ->orderBy('staff_freedays_reasons.description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "states":
                $oSelect = State::select('states.id as value', 'states.description->'.app()->getLocale().' as option')
                    ->join('states_models as sm', function ($join) use ($vcParameter1) {
                        $join->on( 'sm.states_id', 'states.id')
                        ->where('sm.model', $vcParameter1);
                    })
                    ->orderBy('sm.order', 'asc')
                    ->get()->toArray();
                break;
            case "templates":
                $oSelect = Template::select('id as value', 'description as option')
                    ->when(!empty($vcParameter1), function ($query) use ($vcParameter1) {
                        $query->where('tsections_id', $vcParameter1);
                    })
                    ->when(!empty($vcParameter2), function ($query) use ($vcParameter2) {
                        $query->where('tobjects_id', $vcParameter2);
                    })
                    ->when(!empty($vcParameter3), function ($query) use ($vcParameter3) {
                        $query->where('ttypes_id', $vcParameter3);
                    })
                    ->orderBy('description', 'asc')
                    ->get()->toArray();
                break;
            case "templates_sections":
                $oSelect = TemplatesSection::select('id as value', 'description as option')
                    ->orderBy('description', 'asc')
                    ->get()->toArray();
                break;
            case "templates_objects":
                $oSelect = TemplatesObject::select('id as value', 'description as option')
                    ->orderBy('description', 'asc')
                    ->get()->toArray();
                break;
            case "templates_types":
                $oSelect = TemplatesType::select('id as value', 'description as option')
                    ->orderBy('description', 'asc')
                    ->get()->toArray();
                break;
            case "town_halls":
                $oSelect = TownHall::select('id as value', 'name->'.app()->getLocale().' as option')
                    ->orderBy('name', 'asc')
                    ->get()->toArray();
                break;
            case "treasury_banks":
                $oSelect = TreasuryBank::select('id as value', 'name as option')
                    ->orderBy('name', 'asc')
                    ->get()->toArray();
                break;
            case "treasury_banks_accounts":
                $oSelect = TreasuryBanksAccount::select('treasury_banks_accounts.id as value')
                    ->selectRaw('concat(b.name, " - ", treasury_banks_accounts.alias) as `option`')
                    ->join('treasury_banks as b','b.id', 'treasury_banks_accounts.banks_id')
                    ->where('treasury_banks_accounts.townhalls_id', session('townhall_id'))
                    ->orderBy('b.name', 'asc')
                    ->get()->toArray();
                break;
            case "treasury_liquidations_concepts_types":
                $oSelect = TreasuryLiquidationsConceptsType::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->orderBy('description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "treasury_liquidations_types":
                $oSelect = TreasuryLiquidationsType::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->where('townhalls_id', session('townhall_id'))
                    ->when(!empty($vcParameter1) && $vcParameter1=='active', function ($query) {
                        $query->where('treasury_liquidations_types.from_date', '<=', Carbon::now())
                        ->where('treasury_liquidations_types.to_date', '>=', Carbon::now());
                    })
                    ->orderBy('description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "treasury_procedures":
                $oSelect = TreasuryProcedure::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->where('townhalls_id', session('townhall_id'))
                    ->when(!empty($vcParameter1), function ($query) use ($vcParameter1) {
                        $query->where('public', $vcParameter1);
                    })
                    ->orderBy('description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "treasury_va_liquidations_dtypes":
                $oSelect = TreasuryVaLiquidationsDtype::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->orderBy('description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "treasury_va_liquidations_types":
                $oSelect = TreasuryVaLiquidationsType::select('id as value', 'description->'.app()->getLocale().' as option')
                    ->orderBy('description->'.app()->getLocale(), 'asc')
                    ->get()->toArray();
                break;
            case "users":
                $oSelect = User::select('users.id as value', 'users.name as option')
                    ->join('users_town_halls as ut', 'ut.users_id', 'users.id')
                    ->where('ut.townhalls_id', session('townhall_id'))
                    ->when(!empty($vcParameter1), function ($query) use ($vcParameter1) {
                        $query->whereNotIn('users.id', $vcParameter1);
                    })
                    ->where('users.active', 1)
                    ->orderBy('users.name', 'asc')
                    ->get()->toArray();
                break;
            case "users_active":
                $oSelect = [
                    ["value" => 0, "option" => "No activos"],
                    ["value" => 1, "option" => "Activos"]
                ];
                break;
            case "weekdays":
                $a = array(1, 2, 3, 4, 5, 6, 7);
                $oSelect = array_map("static::get_day", $a);
                break;
            case "years":
                $iYear = date("Y");
                $iPre = $iYear - (($vcParameter1 != '') ? $vcParameter1 : 5);
                $iPos = $iYear + (($vcParameter2 != '') ? $vcParameter2 : 5);

                for ($i = $iPre; $i <= $iPos; $i++) {
                    $oSelect[] = ["value" => $i, "option" => $i];
                }
                break;
        }

        return $oSelect;
    }
}
