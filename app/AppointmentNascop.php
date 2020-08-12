<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppointmentNascop extends Model
{
    protected $connection = 'mysql_nascop';
    public $table         = 'tbl_appointment';
}
