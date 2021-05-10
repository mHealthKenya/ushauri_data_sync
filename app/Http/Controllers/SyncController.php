<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '6144');

use App\Appointment;
use App\AppointmentFaces;
use App\Broadcast;
use App\BroadcastFaces;
use App\CareGiver;
use App\CareGiverFaces;
use App\Client;
use App\ClientFaces;
use App\ClientOutcome;
use App\ClientOutcomeFaces;
use App\ClientOutgoing;
use App\ClientOutgoingFaces;
use App\DFC;
use App\DFCFaces;
use App\OtherAppType;
use App\OtherAppTypeFaces;
use App\OtherFnlOutcome;
use App\OtherFnlOutcomeFaces;
use App\PMTCT;
use App\PmtctFaces;
use App\SmsQueue;
use App\SmsQueueFaces;
use App\Transit;
use App\TransitFaces;
use App\User;
use Carbon\Carbon;
use App\UserFaces;
use App\UserOutgoing;
use App\UserOutgoingFaces;
use \Mailjet\Resources;

class SyncController extends Controller
{
  public function index()
  {

    $this->syncUsers();
    $this->sync_care_giver();
    $this->syncClients();
    $this->syncAppointments();
    $this->syncPMTCT();
    $this->sync_dfc();
    $this->syncClientOutcomes();
    $this->syncOtherAppType();
    // $this->syncOtherFnlOutcome();t
    // $this->syncBroadcast();t
    // $this->syncSmsQueue();t
    $this->syncTransitClients();
    // $this->syncUserOutgoing();t

    //$this->syncClientOutgoing();t
  }

  public function syncUsers()
  {
    try {
      $max_exisiting_user = UserFaces::max('id') ?? 0;
      $start_time = Carbon::now();
      $a_number = 0;
      $users = User::where('partner_id', 18)->where('id', '>', $max_exisiting_user)->get();
      foreach ($users as $user) {
        $userFaces = UserFaces::find($user->id);
        if (!$userFaces) {
          echo "Inserting new User..." . "<br>";
          UserFaces::insert($user->toArray());
          $a_number = $a_number + 1;
        }
      }
      $updates_users = User::where('partner_id', 18)->where('updated_at', '>', Carbon::now()->subDays(1))->get();
      foreach ($updates_users as $updates_user) {
        $FoundUsers = UserFaces::find($updates_user->id);
        if ($FoundUsers) {
          echo "Updating existing User..." . "<br>";
          UserFaces::whereId($updates_user->id)->update($updates_user->toArray());
          $a_number = $a_number + 1;
        }
      }
      $end_time = Carbon::now();
      
      $this->send_email($start_time, $end_time, $users->count() + $updates_users->count() . " Users", $a_number . " Users", "Users sync");
    }  catch (\Exception $e) {
      $this->send_err_email($e->getMessage(), "Users sync");
    }
  }

  public function syncClients()
  {

    try{
      $max_exisiting_client = ClientFaces::max('id') ?? 0;
      $start_time = Carbon::now();
      $a_number = 0;
      $clients = Client::where('partner_id', 18)
        ->where('id', '>', $max_exisiting_client)
        ->get();
      foreach ($clients as $client) {
        $clientFaces = ClientFaces::find($client->id);

        if (!$clientFaces) {
          echo "Inserting new Client..." . "<br>";
          ClientFaces::insertOrIgnore($client->toArray());
          $a_number = $a_number + 1;
        }
      }

      $updates_availables = Client::where('partner_id', 18)->where('updated_at', '>', Carbon::now()->subDays(1))->get();


      foreach ($updates_availables as $updates_available) {
        $FoundClients = ClientFaces::find($updates_available->id);
        if ($FoundClients) {
          if ($FoundClients->id === $updates_available->id) {
            if ($FoundClients->updated_at < $updates_available->updated_at) {
              echo "Updating existing Client..." . " " . "$FoundClients->id" . "<br>";
              ClientFaces::whereId($updates_available->id)->update($updates_available->toArray());
              $a_number = $a_number + 1;
            }
          } else {
            continue;
          }
        }
      }
      $end_time = Carbon::now();
      
      $this->send_email($start_time, $end_time, $clients->count() + $updates_availables->count() . " Clients", $a_number . " Clients", "Client Sync");
    } catch (\Exception $e) {
      $this->send_err_email($e->getMessage(), "Client Sync");
    }
  }

  public function syncAppointments()
  {
    try{
      $max_exisiting_appointment = AppointmentFaces::max('id') ?? 0;
      $start_time = Carbon::now();
      $a_number = 0;
      $appointments = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')->select('tbl_appointment.*')
        ->where('tbl_appointment.id', '>', $max_exisiting_appointment)
        ->where('tbl_client.partner_id', 18)->get();
      foreach ($appointments as $appointment) {
        $appFaces = AppointmentFaces::find($appointment->id);
        if (!$appFaces) {
          $check_client_existence = ClientFaces::find($appointment->client_id);
          if ($check_client_existence) {
            echo "Insert new Appointment..." . "<br>";
            AppointmentFaces::insert($appointment->toArray());
            $a_number = $a_number + 1;
          }
        }
      }

      $appointment_updates = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')->select('tbl_appointment.*')->where('tbl_appointment.updated_at', '>', Carbon::now()->subDays(1))->where('tbl_client.partner_id', 18)->get();

      foreach ($appointment_updates as $appointment_update) {
        $FoundApp = AppointmentFaces::find($appointment_update->id);
        if ($FoundApp) {
          if ($FoundApp->id === $appointment_update->id && $FoundApp->client_id === $appointment_update->client_id) {
            if ($FoundApp->updated_at < $appointment_update->updated_at) {
              echo "Updating existing appointment..." .  "<br>";
              AppointmentFaces::whereId($appointment_update->id)->update($appointment_update->toArray());
              $a_number = $a_number + 1;
            }
          } else {
            continue;
          }
        }
      }
      $end_time = Carbon::now();
      
      $this->send_email($start_time, $end_time, $appointments->count() + $appointment_updates->count() . " Appointments", $a_number . " Appointments", "Appointments sync");
    } catch (\Exception $e) {
      $this->send_err_email($e->getMessage(), "Appointments sync");
    }
  }

  public function syncClientOutcomes()
  {
    try {
      $max_exisiting_client_outcome = ClientOutcomeFaces::max('id') ?? 0;
      $a_number = 0;
      $start_time = Carbon::now();
      $client_outcomes = ClientOutcome::join('tbl_client', 'tbl_clnt_outcome.client_id', '=', 'tbl_client.id')->select('tbl_clnt_outcome.*')
        ->where('tbl_clnt_outcome.id', '>', $max_exisiting_client_outcome)
        ->where('tbl_client.partner_id', 18)->get();
      foreach ($client_outcomes as $client_outcome) {
        $outcomesFaces = ClientOutcomeFaces::find($client_outcome->id);
        if (!$outcomesFaces) {
          echo "Inserting Client Outcomes..." .  "<br>";
          ClientOutcomeFaces::insertOrIgnore($client_outcome->toArray());
          $a_number += 1;
        }
      }
      $end_time = Carbon::now();
      $this->send_email($start_time, $end_time, $client_outcomes->count() . " ClientOutcome", $a_number . " ClientOutcome", "ClientOutcome");
    } catch (\Exception $e) {
      $this->send_err_email($e->getMessage(), "Appointments sync");
    }
  }

  public function syncClientOutgoing()
  {
    try{
      $max_exisiting_client_outgoing = ClientOutgoingFaces::max('id') ?? 0;
      $client_outgoings = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')->select('tbl_clnt_outgoing.*')
        ->where('tbl_clnt_outgoing.id', '>', $max_exisiting_client_outgoing)
        ->where('tbl_client.partner_id', 18)->get();
      foreach ($client_outgoings as $client_outgoing) {
        $clientOutgoingFaces = ClientOutgoingFaces::find($client_outgoing->id);
        if (!$clientOutgoingFaces) {
          ClientOutgoingFaces::insertOrIgnore($client_outgoing->toArray());
        }
      }
    } catch (\Exception $e) {
      $this->send_err_email($e->getMessage(), "Client outgoing");
    }
  }

  public function syncUserOutgoing()
  {
    $max_exisiting_user_outgoing = UserOutgoingFaces::max('id') ?? 0;
    $user_outgoings = UserOutgoing::join('tbl_users', 'tbl_usr_outgoing.clnt_usr_id', '=', 'tbl_users.id')->select('tbl_usr_outgoing.*')
      ->where('tbl_usr_outgoing.id', '>', $max_exisiting_user_outgoing)
      ->where('tbl_users.partner_id', 18)->get();
    foreach ($user_outgoings as $user_outgoing) {
      $userOutgoingFaces = UserOutgoingFaces::find($user_outgoing->id);
      if (!$userOutgoingFaces) {
        UserOutgoingFaces::insert($user_outgoing->toArray());
      }
    }
  }

  public function syncOtherAppType()
  {
    try{
      $max_exisiting_other_app_type = OtherAppTypeFaces::max('id') ?? 0;
      $other_app_types = OtherAppType::join('tbl_users', 'tbl_other_appointment_types.created_by', '=', 'tbl_users.id')
        ->join('tbl_appointment', 'tbl_other_appointment_types.appointment_id', '=', 'tbl_appointment.id')
        ->select('tbl_other_appointment_types.*')
        ->where('tbl_other_appointment_types.id', '>', $max_exisiting_other_app_type)
        ->where('tbl_users.partner_id', 18)->get();
      foreach ($other_app_types as $other_app_type) {
        $otherAppFaces = OtherAppTypeFaces::find($other_app_type->id);
        if (!$otherAppFaces) {
          $check_app_existence = AppointmentFaces::find($other_app_type->appointment_id);
          if ($check_app_existence) {
            echo "Insert other app type..." . "<br>";
            OtherAppTypeFaces::insertOrIgnore($other_app_type->toArray());
          }
        }
      } 
    } catch (\Exception $e){
      $this->send_err_email($e->getMessage(), "Other App Type sync");
    }
  }

  public function syncOtherFnlOutcome()
  {
    $max_existing_other_fnl_outcome = OtherFnlOutcomeFaces::max('id') ?? 0;
    $other_outcomes = OtherFnlOutcome::join('tbl_users', 'tbl_other_final_outcome.created_by', '=', 'tbl_users.id')->select('tbl_other_final_outcome.*')
      ->where('tbl_other_final_outcome.id', '>', $max_existing_other_fnl_outcome)
      ->where('tbl_users.partner_id', 18)->get();
    foreach ($other_outcomes as $other_outcome) {
      $otherfnlOutocmeFaces = OtherFnlOutcomeFaces::find($other_outcome->id);
      if (!$otherfnlOutocmeFaces) {
        $check_Outcome_existence = ClientOutcomeFaces::find($other_outcome->client_outcome_id);
        if ($check_Outcome_existence) {
          echo "Insert other final outcome..." . "<br>";
          OtherFnlOutcomeFaces::insertOrIgnore($other_outcome->toArray());
        }
      }
    }
  }

  public function syncBroadcast()
  {
    $max_existing_broadcast = BroadcastFaces::max('id') ?? 0;
    $broadcasts = Broadcast::join('tbl_users', 'tbl_broadcast.created_by', '=', 'tbl_users.id')->select('tbl_broadcast.*')
      ->where('tbl_broadcast.id', '>', $max_existing_broadcast)
      ->where('tbl_users.partner_id', 18)->get();
    foreach ($broadcasts as $broadcast) {
      $broadcastFaces = BroadcastFaces::find($broadcast->id);
      if (!$broadcastFaces) {
        BroadcastFaces::insertOrIgnore($broadcast->toArray());
      }
    }
  }

  public function syncSmsQueue()
  {
    $max_existing_queues = SmsQueueFaces::max('id') ?? 0;
    $sms_queues = SmsQueue::join('tbl_partner_facility', 'tbl_sms_queue.mfl_code', '=', 'tbl_partner_facility.mfl_code')->select('tbl_sms_queue.*')
      ->where('tbl_sms_queue.id', '>', $max_existing_queues)
      ->where('tbl_partner_facility.partner_id', 18)->limit(20000)->get();
    foreach ($sms_queues as $sms_queue) {
      $smsQueueFaces = SmsQueueFaces::find($sms_queue->id);
      if (!$smsQueueFaces) {
        SmsQueueFaces::insertOrIgnore($sms_queue->toArray());
      }
    }
  }

  public function syncTransitClients()
  {
    try{
      $max_existing_transits = TransitFaces::max('id') ?? 0;
      $sms_transits = Transit::join('tbl_client', 'tbl_transit_app.client_id', '=', 'tbl_client.id')->select('tbl_transit_app.*')
        ->where('tbl_transit_app.id', '>', $max_existing_transits)
        ->where('tbl_client.partner_id', 18)->get();
      foreach ($sms_transits as $sms_transit) {
        $transitFaces = TransitFaces::find($sms_transit->id);
        if (!$transitFaces) {
          TransitFaces::insert($sms_transit->toArray());
        }
      }
    } catch (\Exception $e){
      $this->send_err_email($e->getMessage(), "Transit Client sync");
    }
  }

  public function syncPMTCT()
  {
    try{
      $max_exisiting_mothers = PmtctFaces::max('id') ?? 0;
      $mother_module = PMTCT::join('tbl_users', 'tbl_pmtct.created_by', '=', 'tbl_users.id')->select('tbl_pmtct.*')
        ->where('tbl_pmtct.id', '>', $max_exisiting_mothers)
        ->where('tbl_users.partner_id', 18)->get();
      foreach ($mother_module as $mother) {
        $motherFACES = PmtctFaces::find($mother->id);
        if (!$motherFACES) {
          echo "Insert pmtct clients..." . "<br>";
          PmtctFaces::insertOrIgnore($mother->toArray());
        }
      }
    } catch (\Exception $e){
      $this->send_err_email($e->getMessage(), "PMTCT sync");
    }
  }

  public function sync_dfc()
  {
    try{
      $max_exisiting_dfc = DFCFaces::max('id') ?? 0;
      $dfc_module = DFC::join('tbl_client', 'tbl_dfc_module.client_id', '=', 'tbl_client.id')->select('tbl_dfc_module.*')
        ->where('tbl_dfc_module.id', '>', $max_exisiting_dfc)
        ->where('tbl_client.partner_id', 18)->get();
      foreach ($dfc_module as $dfc) {
        $dfcFACES = DFCFaces::find($dfc->id);
        if (!$dfcFACES) {
          echo "Insert dfc clients..." . "<br>";
          DFCFaces::insertOrIgnore($dfc->toArray());
        }
      }
    } catch (\Exception $e){
      $this->send_err_email($e->getMessage(), "DFC sync");
    }
  }

  public function sync_care_giver()
  {
    try {
      $max_exisiting_care_giver = CareGiverFaces::max('id') ?? 0;
      $a_number = 0;
      $start_time = Carbon::now();
      $care_givers = CareGiver::join('tbl_users', 'tbl_caregiver_not_on_care.created_by', '=', 'tbl_users.id')->select('tbl_caregiver_not_on_care.*')
        ->where('tbl_caregiver_not_on_care.id', '>', $max_exisiting_care_giver)
        ->where('tbl_users.partner_id', 18)->get();
      foreach ($care_givers as $giver) {
        $careFACES = CareGiverFaces::find($giver->id);
        if (!$careFACES) {
          echo "Insert care giver details..." . "<br>";
          CareGiverFaces::insertOrIgnore($giver->toArray());
          $a_number += 1;
        }
      }
      $end_time = Carbon::now();
      
      $this->send_email($start_time, $end_time, $care_givers->count() . " care givers", $a_number . " care givers", "Caregiver sync");
    } catch (\Exception $e){
      $this->send_err_email($e->getMessage(), "Caregiver sync");
    }
  }
  
  public function send_email($start_time, $end_time, $numbers, $a_number, $subject)
  {
    $mj = new \Mailjet\Client('c6eb234e3f6a77aeaa6352f2ad1d4a04','b73d7ee669d9a48b1f2cea1938f513d0',true,['version' => 'v3.1']);
    $body = [
      'Messages' => [
        [
          'From' => [
            'Email' => "datasync@mhealthkenya.co.ke",
            'Name' => "datasync"
          ],
          'To' => [
            [
              'Email' => "cbrian@mhealthkenya.org",
              'Name' => "brian"
            ],
            [
              'Email' => "rodhiambo@mhealthkenya.org",
              'Name' => "ronald"
            ]
          ],
          'Subject' => "Ushauri Sync" . $subject,
          'TextPart' => "My first Mailjet email",
          'HTMLPart' => "<h3>Started at: ". $start_time ."<br>Started at: ". $end_time ."<br> Records found ". $numbers ."<br> Records added ". $a_number ." </h3><br />Regards!",
          'CustomID' => "AppGettingStartedTest"
        ]
      ]
    ];
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    $response->success() && var_dump($response->getData());
  }
  
  public function send_err_email($error, $subject)
  {
    $mj = new \Mailjet\Client('c6eb234e3f6a77aeaa6352f2ad1d4a04','b73d7ee669d9a48b1f2cea1938f513d0',true,['version' => 'v3.1']);
    $body = [
      'Messages' => [
        [
          'From' => [
            'Email' => "datasync@mhealthkenya.co.ke",
            'Name' => "datasync"
          ],
          'To' => [
            [
              'Email' => "cbrian@mhealthkenya.org",
              'Name' => "brian"
            ],
            [
              'Email' => "rodhiambo@mhealthkenya.org",
              'Name' => "ronald"
            ]
          ],
          'Subject' => "Error " . $subject,
          'TextPart' => "My first Mailjet email",
          'HTMLPart' => "<h3>Error occured: ". $error ."</h3><br /> Regards!",
          'CustomID' => "AppGettingStartedTest"
        ]
      ]
    ];
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    $response->success() && var_dump($response->getData());
  }
}
