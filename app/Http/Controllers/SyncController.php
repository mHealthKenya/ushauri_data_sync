<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

use App\Client;
use App\ClientFaces;

class SyncController extends Controller
{
 public function index()
 {
  $this->syncClients();
 }

 public function syncClients()
 {
  $max_exisiting_client = ClientFaces::max('id');
  $clients              = Client::where('partner_id', 18)->where('id', '>', $max_exisiting_client)->get();
  foreach ($clients as $client) {
   ClientFaces::insert($client->toArray());
  }
 }
}
