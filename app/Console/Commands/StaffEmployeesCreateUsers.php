<?php

namespace App\Console\Commands;

use App\Actions\StaffEmployees\CreateEmployeeUser;
use Illuminate\Console\Command;
use App\Models\SportsInstallationsReservation;
use App\Models\StaffEmployee;
use App\Models\TreasuryBillingCode;
use Carbon\Carbon;

class StaffEmployeesCreateUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staff-employees:create-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create users for employees that do not have one';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $employees = StaffEmployee::all();

        foreach ($employees as $employee) {
            if (empty($employee->user) && (empty($employee->contract_end) || $employee->contract_end > Carbon::now())) {
                CreateEmployeeUser::handle($employee);
            }
        }

        return 0;
    }
}
