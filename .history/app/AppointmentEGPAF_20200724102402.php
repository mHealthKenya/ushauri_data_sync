<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppointmentFaces extends Model
{
 protected $connection = 'mysql_faces';
 public $table         = 'tbl_appointment';
}
