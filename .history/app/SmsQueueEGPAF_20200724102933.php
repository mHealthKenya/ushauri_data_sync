<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsQueueFaces extends Model
{
    protected $connection = 'mysql_faces';
    public $table         = 'tbl_sms_queue';
}
