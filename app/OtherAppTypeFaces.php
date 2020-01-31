<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtherAppTypeFaces extends Model
{
    protected $connection = 'mysql_faces';
    public $table         = 'tbl_other_appointment_types';
}
