<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtherAppTypeEGPAF extends Model
{
    protected $connection = 'mysql_egpaf';
    public $table         = 'tbl_other_appointment_types';
}
