<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsQueueEGPAF extends Model
{
    protected $connection = 'mysql_egpaf';
    public $table         = 'tbl_sms_queue';
}
