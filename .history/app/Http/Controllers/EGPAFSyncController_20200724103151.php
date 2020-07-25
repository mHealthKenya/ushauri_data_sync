<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '4096M');

use App\Appointment;
use App\AppointmentEGPAF;
use App\Broadcast;
use App\BroadcastEGPAF;
use App\Client;
use App\ClientEGPAF;
use App\ClientOutcome;
use App\ClientOutcomeEGPAF;
use App\ClientOutgoing;
use App\ClientOutgoingEGPAF;
use App\OtherAppType;
use App\OtherAppTypeEGPAF;
use App\OtherFnlOutcome;
use App\OtherFnlOutcomeEGPAF;
use App\SmsQueue;
use App\SmsQueueEGPAF;
use App\Transit;
use App\TransitEGPAF;
use App\User;
use Carbon\Carbon;
use App\UserEGPAF;
use App\UserOutgoing;
use App\UserOutgoingEGPAF;

class SyncController extends Controller
{
    public function index()
    {
        $this->syncUsers();
        $this->syncClients();
        $this->syncClientOutcomes();
        $this->syncOtherAppType();
        $this->syncOtherFnlOutcome();
        $this->syncBroadcast();
        $this->syncSmsQueue();
        $this->syncTransitClients();
        //$this->syncClientOutgoing();
    //$this->syncAppointments();
    //$this->syncUserOutgoing();
    }

    public function syncUsers()
    {
        $max_exisiting_user = UserEGPAF::max('id') ?? 0;
        $users = User::where('partner_id', 18)->where('id', '>', $max_exisiting_user)->get();
        foreach ($users as $user) {
            $userEGPAF = UserEGPAF::find($user->id);
            if (!$userEGPAF) {
                echo "Inserting new User..." . "<br>";
                UserEGPAF::insert($user->toArray());
            }
        }
        $updates_users = User::where('partner_id', 18)->where('updated_at', '>', Carbon::now()->subDays(1))->get();
        foreach ($updates_users as $updates_user) {
            $FoundUsers = UserEGPAF::find($updates_user->id);
            if ($FoundUsers) {
                echo "Updating existing User..." . "<br>";
                UserEGPAF::whereId($updates_user->id)->update($updates_user->toArray());
            }
        }
    }
    public function syncClients()
    {
        $max_exisiting_client = ClientEGPAF::max('id') ?? 0;

        $clients = Client::where('partner_id', 18)->where('id', '>', $max_exisiting_client)->get();
        foreach ($clients as $client) {
            $clientEGPAF = ClientEGPAF::find($client->id);

            if (!$clientEGPAF) {
                echo "Inserting new Client..." . "<br>";
                ClientEGPAF::insertOrIgnore($client->toArray());
            }
        }

        $updates_availables = Client::where('partner_id', 18)->where('updated_at', '>', Carbon::now()->subDays(1))->get();
        foreach ($updates_availables as $updates_available) {
            $FoundClients = ClientEGPAF::find($updates_available->id);
            if ($FoundClients) {
                if ($FoundClients->id === $updates_available->id && $FoundClients->clinic_number === $updates_available->clinic_number) {
                    if ($FoundClients->updated_at < $updates_available->updated_at) {
                        echo "Updating existing Client..." . "<br>";
                        ClientEGPAF::whereId($updates_available->id)->update($updates_available->toArray());
                    }
                } else {
                    continue;
                }
            }
        }
    }

    public function syncAppointments()
    {
        $max_exisiting_appointment = AppointmentEGPAF::max('id') ?? 0;
        $appointments = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')->select('tbl_appointment.*')->where('tbl_appointment.id', '>', $max_exisiting_appointment)->where('tbl_client.partner_id', 18)->get();
        foreach ($appointments as $appointment) {
            $appEGPAF = AppointmentEGPAF::find($appointment->id);
            if (!$appEGPAF) {
                $check_client_existence = ClientEGPAF::find($appointment->client_id);
                if ($check_client_existence) {
                    echo "Insert new Appointment..." . "<br>";
                    AppointmentEGPAF::insert($appointment->toArray());
                }
            }
        }
        $appointment_updates = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')->select('tbl_appointment.*')->where('tbl_appointment.updated_at', '>', Carbon::now()->subDays(1))->where('tbl_client.partner_id', 18)->get();
        foreach ($appointment_updates as $appointment_update) {
            $FoundApp = AppointmentEGPAF::find($appointment_update->id);
            if ($FoundApp) {
                if ($FoundApp->id === $appointment_update->id && $FoundApp->client_id === $appointment_update->client_id) {
                    if ($FoundApp->updated_at < $appointment_update->updated_at) {
                        echo "Updating existing appointment..." .  "<br>";
                        AppointmentEGPAF::whereId($appointment_update->id)->update($appointment_update->toArray());
                    }
                } else {
                    continue;
                }
            }
        }
    }

    public function syncClientOutcomes()
    {
        $max_exisiting_client_outcome = ClientOutcomeEGPAF::max('id') ?? 0;
        $client_outcomes = ClientOutcome::join('tbl_client', 'tbl_clnt_outcome.client_id', '=', 'tbl_client.id')->select('tbl_clnt_outcome.*')->where('tbl_clnt_outcome.id', '>', $max_exisiting_client_outcome)->where('tbl_client.partner_id', 18)->get();
        foreach ($client_outcomes as $client_outcome) {
            $outcomesEGPAF = ClientOutcomeEGPAF::find($client_outcome->id);
            if (!$outcomesEGPAF) {
                echo "Inserting Client Outcomes..." .  "<br>";
                ClientOutcomeEGPAF::insertOrIgnore($client_outcome->toArray());
            }
        }
    }

    public function syncClientOutgoing()
    {
        $max_exisiting_client_outgoing = ClientOutgoingEGPAF::max('id') ?? 0;
        $client_outgoings = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')->select('tbl_clnt_outgoing.*')->where('tbl_clnt_outgoing.id', '>', $max_exisiting_client_outgoing)->where('tbl_client.partner_id', 18)->get();
        foreach ($client_outgoings as $client_outgoing) {
            $clientOutgoingEGPAF = ClientOutgoingEGPAF::find($client_outgoing->id);
            if (!$clientOutgoingEGPAF) {
                ClientOutgoingEGPAF::insertOrIgnore($client_outgoing->toArray());
            }
        }
    }

    public function syncUserOutgoing()
    {
        $max_exisiting_user_outgoing = UserOutgoingEGPAF::max('id') ?? 0;
        $user_outgoings = UserOutgoing::join('tbl_users', 'tbl_usr_outgoing.clnt_usr_id', '=', 'tbl_users.id')->select('tbl_usr_outgoing.*')->where('tbl_usr_outgoing.id', '>', $max_exisiting_user_outgoing)->where('tbl_users.partner_id', 18)->get();
        foreach ($user_outgoings as $user_outgoing) {
            $userOutgoingEGPAF = UserOutgoingEGPAF::find($user_outgoing->id);
            if (!$userOutgoingEGPAF) {
                UserOutgoingEGPAF::insert($user_outgoing->toArray());
            }
        }
    }

    public function syncOtherAppType()
    {
        $max_exisiting_other_app_type = OtherAppTypeEGPAF::max('id') ?? 0;
        $other_app_types = OtherAppType::join('tbl_users', 'tbl_other_appointment_types.created_by', '=', 'tbl_users.id')
      ->join('tbl_appointment', 'tbl_other_appointment_types.appointment_id', '=', 'tbl_appointment.id')
      ->select('tbl_other_appointment_types.*')->where('tbl_other_appointment_types.id', '>', $max_exisiting_other_app_type)
      ->where('tbl_users.partner_id', 18)->get();
        foreach ($other_app_types as $other_app_type) {
            $otherAppEGPAF = OtherAppTypeEGPAF::find($other_app_type->id);
            if (!$otherAppEGPAF) {
                $check_app_existence = AppointmentEGPAF::find($other_app_type->appointment_id);
                if ($check_app_existence) {
                    echo "Insert other app type..." . "<br>";
                    OtherAppTypeEGPAF::insertOrIgnore($other_app_type->toArray());
                }
            }
        }
    }

    public function syncOtherFnlOutcome()
    {
        $max_existing_other_fnl_outcome = OtherFnlOutcomeEGPAF::max('id') ?? 0;
        $other_outcomes = OtherFnlOutcome::join('tbl_users', 'tbl_other_final_outcome.created_by', '=', 'tbl_users.id')->select('tbl_other_final_outcome.*')->where('tbl_other_final_outcome.id', '>', $max_existing_other_fnl_outcome)->where('tbl_users.partner_id', 18)->get();
        foreach ($other_outcomes as $other_outcome) {
            $otherfnlOutocmeEGPAF = OtherFnlOutcomeEGPAF::find($other_outcome->id);
            if (!$otherfnlOutocmeEGPAF) {
                $check_Outcome_existence = ClientOutcomeEGPAF::find($other_outcome->client_outcome_id);
                if ($check_Outcome_existence) {
                    echo "Insert other final outcome..." . "<br>";
                    OtherFnlOutcomeEGPAF::insertOrIgnore($other_outcome->toArray());
                }
            }
        }
    }

    public function syncBroadcast()
    {
        $max_existing_broadcast = BroadcastEGPAF::max('id') ?? 0;
        $broadcasts = Broadcast::join('tbl_users', 'tbl_broadcast.created_by', '=', 'tbl_users.id')->select('tbl_broadcast.*')->where('tbl_broadcast.id', '>', $max_existing_broadcast)->where('tbl_users.partner_id', 18)->get();
        foreach ($broadcasts as $broadcast) {
            $broadcastEGPAF = BroadcastEGPAF::find($broadcast->id);
            if (!$broadcastEGPAF) {
                BroadcastEGPAF::insertOrIgnore($broadcast->toArray());
            }
        }
    }

    public function syncSmsQueue()
    {
        $max_existing_queues = SmsQueueEGPAF::max('id') ?? 0;
        $sms_queues = SmsQueue::join('tbl_partner_facility', 'tbl_sms_queue.mfl_code', '=', 'tbl_partner_facility.mfl_code')->select('tbl_sms_queue.*')->where('tbl_sms_queue.id', '>', $max_existing_queues)->where('tbl_partner_facility.partner_id', 18)->limit(20000)->get();
        foreach ($sms_queues as $sms_queue) {
            $smsQueueEGPAF = SmsQueueEGPAF::find($sms_queue->id);
            if (!$smsQueueEGPAF) {
                SmsQueueEGPAF::insertOrIgnore($sms_queue->toArray());
            }
        }
    }

    public function syncTransitClients()
    {
        $max_existing_transits = TransitEGPAF::max('id') ?? 0;
        $sms_transits = Transit::join('tbl_client', 'tbl_transit_app.client_id', '=', 'tbl_client.id')->select('tbl_transit_app.*')->where('tbl_transit_app.id', '>', $max_existing_transits)->where('tbl_client.partner_id', 18)->get();
        foreach ($sms_transits as $sms_transit) {
            $transitEGPAF = TransitEGPAF::find($sms_transit->id);
            if (!$transitEGPAF) {
                TransitEGPAF::insert($sms_transit->toArray());
            }
        }
    }
}
