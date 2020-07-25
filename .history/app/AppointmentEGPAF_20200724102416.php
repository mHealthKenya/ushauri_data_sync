<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppointmentEGPAF extends Model
{
    protected $connection = 'mysql_egpaf';
    public $table         = 'tbl_appointment';
}
