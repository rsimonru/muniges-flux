<?php

namespace App\Jobs;

use App\Classes\SigingsFile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\SigningsFinishLoadExcel;
use App\Models\StaffEmployee;
use App\Models\StaffSigning;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SigningLoadExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $path;
    public $load_signings;
    public $townhalls_id;
    public $user_id;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct($path, $load_signings, $user_id, $townhalls_id)
	{
        $this->path = $path;
        $this->load_signings = $load_signings;
        $this->townhalls_id = $townhalls_id;
        $this->user_id = $user_id;
	}

    /**
     * Handle the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = SigingsFile::processExcel($this->path, $this->load_signings, $this->townhalls_id);

        event(new SigningsFinishLoadExcel($this->user_id, $result));
    }
}
