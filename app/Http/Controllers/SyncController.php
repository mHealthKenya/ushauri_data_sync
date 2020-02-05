<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '2048M');

use App\Appointment;
use App\AppointmentFaces;
use App\Broadcast;
use App\BroadcastFaces;
use App\Client;
use App\ClientFaces;
use App\ClientOutcome;
use App\User;
use App\ClientOutcomeFaces;
use App\ClientOutgoing;
use App\ClientOutgoingFaces;
use App\OtherAppType;
use App\OtherAppTypeFaces;
use App\OtherFnlOutcome;
use App\OtherFnlOutcomeFaces;
use App\SmsQueue;
use App\SmsQueueFaces;
use App\Transit;
use App\TransitFaces;
use App\UserFaces;
use App\UserOutgoing;
use App\UserOutgoingFaces;

class SyncController extends Controller
{
  public function index()
  {
    // $this->syncUsers();
    $this->syncClients();
    //$this->syncAppointments();
    //$this->syncClientOutcomes();
    //$this->syncClientOutgoing();
    // $this->syncUserOutgoing();
    // $this->syncOtherAppType();
    // $this->syncOtherFnlOutcome();
    // $this->syncBroadcast();
    // $this->syncSmsQueue();
    // $this->syncTransitClients();
  }

  public function syncUsers()
  {
    $max_exisiting_user = UserFaces::max('id') ?? 0;
    $users              = User::where('partner_id', 18)->where('id', '>', $max_exisiting_user)->get();
    foreach ($users as $user) {
      UserFaces::insert($user->toArray());
    }
  }
  public function syncClients()
  {
    $max_exisiting_client = ClientFaces::max('id') ?? 0;

    $clients = Client::where('partner_id', 18)->where('id', '>', $max_exisiting_client)->limit(1000)->get();
    foreach ($clients as $client) {
      if ($client->created_at == $client->updated_at) {
        //ClientFaces::insert($client->toArray());
        echo "Hawa" . "<br>";
      } else {
        //ClientFaces::update($client->toArray());
        echo "Wanafaa Update" . "<br>";
      }
    }
  }

  public function syncAppointments()
  {
    $max_exisiting_appointment = AppointmentFaces::max('id') ?? 0;
    $appointments              = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')->select('tbl_appointment.*')->where('tbl_appointment.id', '>', $max_exisiting_appointment)->where('tbl_client.partner_id', 18)->get();
    foreach ($appointments as $appointment) {
      AppointmentFaces::insert($appointment->toArray());
    }
  }

  public function syncClientOutcomes()
  {
    $max_exisiting_client_outcome = ClientOutcomeFaces::max('id') ?? 0;
    $client_outcomes              = ClientOutcome::join('tbl_client', 'tbl_clnt_outcome.client_id', '=', 'tbl_client.id')->select('tbl_clnt_outcome.*')->where('tbl_clnt_outcome.id', '>', $max_exisiting_client_outcome)->where('tbl_client.partner_id', 18)->get();
    foreach ($client_outcomes as $client_outcome) {
      ClientOutcomeFaces::insert($client_outcome->toArray());
    }
  }

  public function syncClientOutgoing()
  {
    $max_exisiting_client_outgoing = ClientOutgoingFaces::max('id') ?? 0;
    $client_outgoings = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')->select('tbl_clnt_outgoing.*')->where('tbl_clnt_outgoing.id', '>', $max_exisiting_client_outgoing)->where('tbl_client.partner_id', 18)->get();
    foreach ($client_outgoings as $client_outgoing) {
      ClientOutgoingFaces::insert($client_outgoing->toArray());
    }
  }

  public function syncUserOutgoing()
  {
    $max_exisiting_user_outgoing = UserOutgoingFaces::max('id') ?? 0;
    $user_outgoings = UserOutgoing::join('tbl_users', 'tbl_usr_outgoing.clnt_usr_id', '=', 'tbl_users.id')->select('tbl_usr_outgoing.*')->where('tbl_usr_outgoing.id', '>', $max_exisiting_user_outgoing)->where('tbl_users.partner_id', 18)->get();
    foreach ($user_outgoings as $user_outgoing) {
      UserOutgoingFaces::insert($user_outgoing->toArray());
    }
  }

  public function syncOtherAppType()
  {
    $max_exisiting_other_app_type = OtherAppTypeFaces::max('id') ?? 0;
    $other_app_types = OtherAppType::join('tbl_users', 'tbl_other_appointment_types.created_by', '=', 'tbl_users.id')->select('tbl_other_appointment_types.*')->where('tbl_other_appointment_types.id', '>', $max_exisiting_other_app_type)->where('tbl_users.partner_id', 18)->get();
    foreach ($other_app_types as $other_app_type) {
      OtherAppTypeFaces::insert($other_app_type->toArray());
    }
  }

  public function syncOtherFnlOutcome()
  {
    $max_existing_other_fnl_outcome = OtherFnlOutcomeFaces::max('id') ?? 0;
    $other_outcomes = OtherFnlOutcome::join('tbl_users', 'tbl_other_final_outcome.created_by', '=', 'tbl_users.id')->select('tbl_other_final_outcome.*')->where('tbl_other_final_outcome.id', '>', $max_existing_other_fnl_outcome)->where('tbl_users.partner_id', 18)->get();
    foreach ($other_outcomes as $other_outcome) {
      OtherFnlOutcomeFaces::insert($other_outcome->toArray());
    }
  }

  public function syncBroadcast()
  {
    $max_existing_broadcast = BroadcastFaces::max('id') ?? 0;
    $broadcasts = Broadcast::join('tbl_users', 'tbl_broadcast.created_by', '=', 'tbl_users.id')->select('tbl_broadcast.*')->where('tbl_broadcast.id', '>', $max_existing_broadcast)->where('tbl_users.partner_id', 18)->get();
    foreach ($broadcasts as $broadcast) {
      BroadcastFaces::insert($broadcast->toArray());
    }
  }

  public function syncSmsQueue()
  {
    $max_existing_queues = SmsQueueFaces::max('id') ?? 0;
    $sms_queues = SmsQueue::join('tbl_partner_facility', 'tbl_sms_queue.mfl_code', '=', 'tbl_partner_facility.mfl_code')->select('tbl_sms_queue.*')->where('tbl_sms_queue.id', '>', $max_existing_queues)->where('tbl_partner_facility.partner_id', 18)->limit(20000)->get();
    foreach ($sms_queues as $sms_queue) {
      SmsQueueFaces::insert($sms_queue->toArray());
    }
  }

  public function syncTransitClients()
  {
    $max_existing_transits = TransitFaces::max('id') ?? 0;
    $sms_transits = Transit::join('tbl_client', 'tbl_transit_app.client_id', '=', 'tbl_client.id')->select('tbl_transit_app.*')->where('tbl_transit_app.id', '>', $max_existing_transits)->where('tbl_client.partner_id', 18)->get();
    foreach ($sms_transits as $sms_transit) {
      TransitFaces::insert($sms_transit->toArray());
    }
  }
}
