<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

use App\Appointment;
use App\AppointmentFaces;
use App\Client;
use App\ClientFaces;

class SyncController extends Controller
{
 public function index()
 {
  $this->syncClients();
  $this->syncAppointments();
 }

 public function syncClients()
 {
  $max_exisiting_client = ClientFaces::max('id');
  $clients              = Client::where('partner_id', 18)->where('id', '>', $max_exisiting_client)->get();
  foreach ($clients as $client) {
   ClientFaces::insert($client->toArray());
  }
 }

 public function syncAppointments()
 {
  $max_exisiting_appointment = AppointmentFaces::max('id');
  $appointments              = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')->where('id', '>', $max_exisiting_appointment)->where('tbl_client.partner_id', 18)->get();
  foreach ($appointments as $appointment) {
   AppointmentFaces::insert($appointment->toArray());
  }
 }
}
