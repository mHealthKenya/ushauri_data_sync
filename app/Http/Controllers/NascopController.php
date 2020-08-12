<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '4096M');

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ClientNascop;
use App\Client;

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

        // $updates_availables = Client::where('partner_id', 18)->where('updated_at', '>', Carbon::now()->subDays(1))->get();
        // foreach ($updates_availables as $updates_available) {
        //     $FoundClients = ClientFaces::find($updates_available->id);
        //     if ($FoundClients) {
        //         if ($FoundClients->id === $updates_available->id && $FoundClients->clinic_number === $updates_available->clinic_number) {
        //             if ($FoundClients->updated_at < $updates_available->updated_at) {
        //                 echo "Updating existing Client..." . "<br>";
        //                 ClientFaces::whereId($updates_available->id)->update($updates_available->toArray());
        //             }
        //         } else {
        //             continue;
        //         }
        //     }
        // }
    }
}
