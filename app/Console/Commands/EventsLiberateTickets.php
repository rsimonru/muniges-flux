<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ShowsTicket;
use App\Models\ShowsTicketsLiberated;
use Carbon\Carbon;

class EventsLiberateTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:liberate-tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Liberate reserved tickets';

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
        $ids = ShowsTicket::select('id')
        ->where('liberation_date','<=', Carbon::now())
        ->where('states_id', config('states.pending'))
        ->where('types_id', config('constants.tickets_types.general'))
        ->get()->toArray();

        if (length($ids)>0) {
            $tickets = ShowsTicket::whereIn('id', $ids)->get();
            $ticketsLiberate = $tickets->map(function (ShowsTicket $model) {
                return [
                    'tickets_id' => $model->id,
                    'data' => json_encode($model->getAttributes()),
                    'created_at' => now(),
                ];
            });
            ShowsTicketsLiberated::insert($ticketsLiberate->toArray());
            ShowsTicket::whereIn('id', $ids)
            ->update([
                'states_id' => config('states.unused'),
                'lock_id' => null,
                'name' => null,
                'vat' => null,
                'email' => null,
                'phone' => null,
                'liberation_date' => null,
                'users_id' => 0,
            ]);
        }

        return 0;
    }
}
