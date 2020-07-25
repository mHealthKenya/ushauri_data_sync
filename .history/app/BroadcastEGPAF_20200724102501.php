<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BroadcastEGPAF extends Model
{
    protected $connection = 'mysql_egpaf';
    public $table         = 'tbl_broadcast';
}
