<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '4096M');

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ClientNascop;
use App\AppointmentNascop;
use App\Client;
use App\Appointment;

class NascopController extends Controller
{
    //
    public function syncClients()
    {
        $max_exisiting_client = ClientNascop::max('id') ?? 0;

        $clients = Client::where('id', '>', $max_exisiting_client)->get();
        foreach ($clients as $client) {
            $clientFaces = ClientNascop::find($client->id);

            if (!$clientFaces) {
                echo "Inserting new Client..." . "<br>";
                ClientNascop::insertOrIgnore($client->toArray());
            }
        }
    }
    public function syncAppointments()
    {
        $max_exisiting_appointment = AppointmentNascop::max('id') ?? 0;
        $appointments = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')->select('tbl_appointment.*')->where('tbl_appointment.id', '>', $max_exisiting_appointment)->get();
        foreach ($appointments as $appointment) {
            $appFaces = AppointmentNascop::find($appointment->id);
            if (!$appFaces) {
                $check_client_existence = AppointmentNascop::find($appointment->client_id);
                if ($check_client_existence) {
                    echo "Insert new Appointment..." . "<br>";
                    AppointmentNascop::insertOrIgnore($appointment->toArray());
                }
            }
        }
    }
}
