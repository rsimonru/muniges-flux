<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');


Schedule::command('sports:cancel-reservations')->everyMinute();
Schedule::command('sports:cancel-passes')->everyMinute();
Schedule::command('events:liberate-tickets')->everyMinute();
Schedule::command('models:year-start-init')->yearly();
