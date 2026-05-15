<?php

namespace App\Listeners;

use App\Classes\Panel;
use App\Classes\PdfDocument;
use App\Classes\PdfFile;
use App\Events\BillingCodePayed;
use App\Models\Show;
use App\Models\ShowsTicket;
use App\Models\SportsEventsRegistrationsPayment;
use App\Models\SportsInstallation;
use App\Models\Template;
use App\Models\TreasuryBillingCode;
use App\Models\TreasuryBillingCodesSequential;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Http\UploadedFile;

class UpdateBillingCodeModel
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BillingCodePayed  $event
     * @return void
     */
    public function handle(BillingCodePayed $event)
    {
        //
        if ($event->model == 'SportsInstallationsReservation') {
            $model = "App\\Models\\".$event->model;
            $reservation = $model::emtGet($event->models_id);
            $reservation->states_id = config('states.rented');
            $reservation->save();

            $installation = SportsInstallation::emtGet($reservation->sports_installations_resource->sports_installations_resources_group->installations_id);
            $pdf = new PdfDocument();
            $result = $pdf->generateSportsInstallationsReservation($reservation->id);
            if ($result) {
                $tmp_path = tempnam(sys_get_temp_dir(), 'PDF');
                file_put_contents($tmp_path, $result['content']);
                $file = new UploadedFile($tmp_path, $result['filename']);
                $result = Panel::sendAttachment(0, $result['filename'], $file);
                $body = view('mail.default',[
                    'body' => __('sports.reservation_mail_body'),
                ])->render();
                Panel::sendMail(0, env('MAIL_FROM_ADDRESS'), $reservation->email, '', '', __('sports.reservation_mail_subject'), $body, $result['iResult']);
                if(!empty($installation->notify_to)) {
                    Panel::sendMail(0, env('MAIL_FROM_ADDRESS'), $installation->notify_to, '', '', __('sports.reservation_mail_subject'), $body, $result['iResult']);
                }
                unlink($tmp_path);
            }
        } elseif ($event->model == 'SportsInstallationsPass') {
            $model = "App\\Models\\".$event->model;
            $pass = $model::emtGet($event->models_id);
            $pass->states_id = config('states.rented');
            $pass->save();

            $installation = SportsInstallation::emtGet($pass->sports_installations_config_pass->sports_installations_resources_group->installations_id);
            $pdf = new PdfDocument();
            $result = $pdf->generateSportsInstallationsPass($pass->id);
            if ($result) {
                $tmp_path = tempnam(sys_get_temp_dir(), 'PDF');
                file_put_contents($tmp_path, $result['content']);
                $file = new UploadedFile($tmp_path, $result['filename']);
                $result = Panel::sendAttachment(0, $result['filename'], $file);
                $body = view('mail.default',[
                    'body' => __('sports.pass_mail_body'),
                ])->render();
                Panel::sendMail(0, env('MAIL_FROM_ADDRESS'), $pass->email, '', '', __('sports.pass_mail_subject'), $body, $result['iResult']);
                if(!empty($installation->notify_to)) {
                    Panel::sendMail(0, env('MAIL_FROM_ADDRESS'), $installation->notify_to, '', '', __('sports.reservation_mail_subject'), $body, $result['iResult']);
                }
                unlink($tmp_path);
            }
        } elseif ($event->model == 'ShowsTicket') {
            $model = "App\\Models\\".$event->model;
            $ticket = ShowsTicket::emtGet($event->models_id);
            if (!empty($ticket->lock_id)) {
                ShowsTicket::where('states_id', config('states.pending'))
                ->where('sessions_id', $ticket->sessions_id)
                ->where('lock_id', $ticket->lock_id)
                ->update([
                    'states_id' => config('states.payed'),
                    'payment_data' => $event->payment_data,
                    'purchase_date' => now(),
                ]);

                $tickets = ShowsTicket::emtGet(0,-1,[],[
                    'states_id' => config('states.payed'),
                    'sessions_id' => $ticket->sessions_id,
                    'lock_id' => $ticket->lock_id,
                ]);
                if (length($tickets)>0) {
                    $email = $tickets->first()->email;
                    $pdf = new PdfDocument();
                    $result = $pdf->generateShowsTickets(array_keys($tickets->toArray()));
                    if ($result) {
                        $tmp_path = tempnam(sys_get_temp_dir(), 'PDF');
                        file_put_contents($tmp_path, $result['content']);
                        $file = new UploadedFile($tmp_path, $result['filename']);
                        $result = Panel::sendAttachment(0, $result['filename'], $file);
                        $body = view('mail.default',[
                            'body' => __('tickets.tickets_mail_body'),
                        ])->render();
                        Panel::sendMail(0, env('MAIL_FROM_ADDRESS'), $email, '', '', __('tickets.tickets_mail_subject'), $body, $result['iResult']);
                        unlink($tmp_path);
                    }
                }
            }
        }
    }
}
