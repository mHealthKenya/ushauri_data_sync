<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

use App\Appointment;
use App\AppointmentFaces;
use App\Client;
use App\ClientFaces;
use App\ClientOutcome;
use App\User;
use App\UserFaces;

class SyncController extends Controller {
 public function index() {
  $this->syncUsers();
  $this->syncClients();
  $this->syncAppointments();
  $this->syncClientOutcomes();
 }

 public function syncUsers() {
  $max_exisiting_user = UserFaces::max('id') ?? 0;
  $users              = User::where('partner_id', 18)->where('id', '>', $max_exisiting_user)->get();
  foreach ($users as $user) {
   UserFaces::insert($user->toArray());
  }
 }
 public function syncClients() {
  $max_exisiting_client = ClientFaces::max('id') ?? 0;

  $clients = Client::where('partner_id', 18)->where('id', '>', $max_exisiting_client)->get();
  foreach ($clients as $client) {
   ClientFaces::insert($client->toArray());
  }
 }

 public function syncAppointments() {
  $max_exisiting_appointment = AppointmentFaces::max('id') ?? 0;
  $appointments              = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')->select('tbl_appointment.*')->where('tbl_appointment.id', '>', $max_exisiting_appointment)->where('tbl_client.partner_id', 18)->get();
  foreach ($appointments as $appointment) {
   AppointmentFaces::insert($appointment->toArray());
  }
 }

 public function syncClientOutcomes() {
  $max_exisiting_client_outcome = ClientOutcomeFaces::max('id') ?? 0;
  $client_outcomes              = ClientOutcome::join('tbl_client', 'tbl_clnt_outcome.client_id', '=', 'tbl_client.id')->select('tbl_clnt_outcome.*')->where('tbl_clnt_outcome.id', '>', $max_exisiting_client_outcome)->where('tbl_client.partner_id', 18)->get();
  foreach ($client_outcomes as $client_outcome) {
   ClientOutcomeFaces::insert($client_outcome->toArray());
  }
 }
}
