<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;
use App\Events\SigningsFinishImport;
use App\Models\StaffEmployee;
use App\Models\StaffSigning;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SigningImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $signings;
    public $fields_columns;
    public $users_id;
    public $townhalls_id;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct($users_id, $townhalls_id, $signings, $fields_columns)
	{
        $this->users_id = $users_id;
        $this->townhalls_id = $townhalls_id;
		$this->signings = $signings;
        $this->fields_columns = $fields_columns;
	}

    /**
     * Handle the job.
     *
     * @return void
     */
    public function handle()
    {
        $signings = [];
        $now = now();
        $employees = StaffEmployee::emtGet(0, -1, [], [
            'townhalls_id' => $this->townhalls_id,
            'with_signing_code' => true,
        ])->keyBy('signing_provider_code');
        foreach ($this->signings as $key => $row) {
            if (isset($employees[$row[$this->fields_columns['employee_code']]])) {
                $periods = [];
                $signing = [];
                $signing['clock_out_at'] = null;
                Log::info('SigningImport: ', $row[$this->fields_columns['date']]);
                $date = Carbon::createFromFormat('Y-m-d H:i:s.u', $row[$this->fields_columns['date']]['date']);
                foreach ($this->fields_columns as $key => $value) {
                    if (strncmp($key, 'sign_out_', 9) == 0 && !empty($row[$this->fields_columns[$key]])) {
                        $signing['clock_out_at'] = Carbon::createFromFormat('d/m/Y H:i', $date->format('d/m/Y'). ' '.$row[$this->fields_columns[$key]]);
                        $sign_index = str_replace('sign_out_', '', $key);
                        $periods[$sign_index] = $row[$this->fields_columns['sign_in_' . $sign_index]] . ' - ' . $row[$this->fields_columns[$key]];
                    }
                }
                $signing['date'] = $date;
                $signing['sign_at'] = empty($row[$this->fields_columns['sign_in_1']]) ? null : Carbon::createFromFormat('d/m/Y H:i', $date->format('d/m/Y'). ' '.$row[$this->fields_columns['sign_in_1']]);
                $signing['townhalls_id'] = $this->townhalls_id;
                $signing['employees_id'] = $employees[$row[$this->fields_columns['employee_code']]]->id;
                $signing['provider_employee_code'] = $row[$this->fields_columns['employee_code']];
                $signing['provider_schedule_code'] = $row[$this->fields_columns['schedule_code']];
                $signing['in'] = 1;
                $signing['out'] = 1;
                $signing['location_in'] = null;
                $signing['location_out'] = null;
                $signing['usignings_rel_id'] = null;
                $signing['wcenter_id'] = null;
                $signing['provider_workcenter_code'] = null;
                $signing['extra_hour'] = false;
                $signing['data'] = json_encode(['periods' => $periods]);
                $signing['signing_time'] = emt_decimalTime($row[$this->fields_columns['presence_time']]);
                $signing['signing_balance'] = emt_decimalTime($row[$this->fields_columns['result_time']]);
                $signing['created_at'] = $now;
                $signing['updated_at'] = $now;
                $signing['created_user'] = $this->users_id;
                $signings[] = $signing;
            }
        }
        StaffSigning::insert($signings);
        event(new SigningsFinishImport($this->users_id));
    }
}
