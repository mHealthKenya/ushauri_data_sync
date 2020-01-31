<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

use App\Appointment;
use App\AppointmentFaces;
use App\Broadcast;
use App\BroadcastFaces;
use App\Client;
use App\ClientFaces;
use App\ClientOutcome;
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
use App\User;
use App\UserFaces;
use App\UserOutgoing;
use App\UserOutgoingFaces;

class SyncController extends Controller {
	public function index() {
		$this->syncUsers();
		$this->syncClients();
		$this->syncAppointments();
		$this->syncClientOutcomes();
		$this->syncClientOutgoing();
		$this->syncUserOutgoing();
		$this->syncOtherAppType();
		$this->syncOtherFnlOutcome();
		$this->syncBroadcast();
		$this->syncSmsQueue();
		$this->syncTransitClients();
	}

	public function syncUsers() {
		$max_exisiting_user = UserFaces::max('id') ?? 0;
		$users = User::where('partner_id', 18)->where('id', '>', $max_exisiting_user)->get();
		foreach ($users as $user) {
			$userFaces = UserFaces::find($user->id);
			if (!$userFaces) {
				UserFaces::insert($user->toArray());
			}
		}
	}
	public function syncClients() {
		$max_exisiting_client = ClientFaces::max('id') ?? 0;

		$clients = Client::where('partner_id', 18)->where('id', '>', $max_exisiting_client)->get();
		foreach ($clients as $client) {
			$clientFaces = ClientFaces::find($client->id);
			if (!$clientFaces) {
				ClientFaces::insert($client->toArray());
			}
		}
	}

	public function syncAppointments() {
		$max_exisiting_appointment = AppointmentFaces::max('id') ?? 0;
		$appointments = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')->select('tbl_appointment.*')->where('tbl_appointment.id', '>', $max_exisiting_appointment)->where('tbl_client.partner_id', 18)->get();
		foreach ($appointments as $appointment) {
			$appFaces = AppointmentFaces::find($appointment->id);
			if (!$appFaces) {
				AppointmentFaces::insert($appointment->toArray());
			}
		}
	}

	public function syncClientOutcomes() {
		$max_exisiting_client_outcome = ClientOutcomeFaces::max('id') ?? 0;
		$client_outcomes = ClientOutcome::join('tbl_client', 'tbl_clnt_outcome.client_id', '=', 'tbl_client.id')->select('tbl_clnt_outcome.*')->where('tbl_clnt_outcome.id', '>', $max_exisiting_client_outcome)->where('tbl_client.partner_id', 18)->get();
		foreach ($client_outcomes as $client_outcome) {
			$outcomesFaces = ClientOutcomeFaces::find($client_outcome->id);
			if (!$outcomesFaces) {
				ClientOutcomeFaces::insert($client_outcome->toArray());
			}
		}
	}

	public function syncClientOutgoing() {
		$max_exisiting_client_outgoing = ClientOutgoingFaces::max('id') ?? 0;
		$client_outgoings = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')->select('tbl_clnt_outgoing.*')->where('tbl_clnt_outgoing.id', '>', $max_exisiting_client_outgoing)->where('tbl_client.partner_id', 18)->get();
		foreach ($client_outgoings as $client_outgoing) {
			$clientOutgoingFaces = ClientOutgoingFaces::find($client_outgoing->id);
			if (!$clientOutgoingFaces) {
				ClientOutgoingFaces::insert($client_outgoing->toArray());
			}
		}
	}

	public function syncUserOutgoing() {
		$max_exisiting_user_outgoing = UserOutgoingFaces::max('id') ?? 0;
		$user_outgoings = UserOutgoing::join('tbl_users', 'tbl_usr_outgoing.clnt_usr_id', '=', 'tbl_users.id')->select('tbl_usr_outgoing.*')->where('tbl_usr_outgoing.id', '>', $max_exisiting_user_outgoing)->where('tbl_users.partner_id', 18)->get();
		foreach ($user_outgoings as $user_outgoing) {
			$userOutgoingFaces = UserOutgoingFaces::find($user_outgoing->id);
			if (!$userOutgoingFaces) {
				UserOutgoingFaces::insert($user_outgoing->toArray());
			}
		}
	}

	public function syncOtherAppType() {
		$max_exisiting_other_app_type = OtherAppTypeFaces::max('id') ?? 0;
		$other_app_types = OtherAppType::join('tbl_users', 'tbl_other_appointment_types.created_by', '=', 'tbl_users.id')->select('tbl_other_appointment_types.*')->where('tbl_other_appointment_types.id', '>', $max_exisiting_other_app_type)->where('tbl_users.partner_id', 18)->get();
		foreach ($other_app_types as $other_app_type) {
			$otherAppFaces = OtherAppTypeFaces::find($other_app_type->id);
			if (!$otherAppFaces) {
				OtherAppTypeFaces::insert($other_app_type->toArray());
			}
		}
	}

	public function syncOtherFnlOutcome() {
		$max_existing_other_fnl_outcome = OtherFnlOutcomeFaces::max('id') ?? 0;
		$other_outcomes = OtherFnlOutcome::join('tbl_users', 'tbl_other_final_outcome.created_by', '=', 'tbl_users.id')->select('tbl_other_final_outcome.*')->where('tbl_other_final_outcome.id', '>', $max_existing_other_fnl_outcome)->where('tbl_users.partner_id', 18)->get();
		foreach ($other_outcomes as $other_outcome) {
			$otherfnlOutocmeFaces = OtherFnlOutcomeFaces::find($other_outcome->id);
			if (!$otherfnlOutocmeFaces) {
				OtherFnlOutcomeFaces::insert($other_outcome->toArray());
			}
		}
	}

	public function syncBroadcast() {
		$max_existing_broadcast = BroadcastFaces::max('id') ?? 0;
		$broadcasts = Broadcast::join('tbl_users', 'tbl_broadcast.created_by', '=', 'tbl_users.id')->select('tbl_broadcast.*')->where('tbl_broadcast.id', '>', $max_existing_broadcast)->where('tbl_users.partner_id', 18)->get();
		foreach ($broadcasts as $broadcast) {
			$broadcastFaces = BroadcastFaces::find($broadcast->id);
			if (!$broadcastFaces) {
				BroadcastFaces::insert($broadcast->toArray());
			}
		}
	}

	public function syncSmsQueue() {
		$max_existing_queues = SmsQueueFaces::max('id') ?? 0;
		$sms_queues = SmsQueue::join('tbl_partner_facility', 'tbl_sms_queue.mfl_code', '=', 'tbl_partner_facility.mfl_code')->select('tbl_sms_queue.*')->where('tbl_sms_queue.id', '>', $max_existing_queues)->where('tbl_partner_facility.partner_id', 18)->limit(20000)->get();
		foreach ($sms_queues as $sms_queue) {
			$smsQueueFaces = SmsQueueFaces::find($sms_queue->id);
			if (!$smsQueueFaces) {
				SmsQueueFaces::insert($sms_queue->toArray());
			}
		}
	}

	public function syncTransitClients() {
		$max_existing_transits = TransitFaces::max('id') ?? 0;
		$sms_transits = Transit::join('tbl_client', 'tbl_transit_app.client_id', '=', 'tbl_client.id')->select('tbl_transit_app.*')->where('tbl_transit_app.id', '>', $max_existing_transits)->where('tbl_client.partner_id', 18)->get();
		foreach ($sms_transits as $sms_transit) {
			$transitFaces = TransitFaces::find($sms_transit->id);
			if (!$transitFaces) {
				TransitFaces::insert($sms_transit->toArray());
			}
		}
	}
}
