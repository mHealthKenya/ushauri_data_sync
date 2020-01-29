<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientFaces extends Model
{
 protected $connection = 'mysql_faces';
 public $table         = 'tbl_client';
}
