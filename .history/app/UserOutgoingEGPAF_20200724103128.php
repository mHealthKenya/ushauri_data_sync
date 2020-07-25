<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserOutgoingEGPAF extends Model
{
    protected $connection = 'mysql_egpaf';
    public $table         = 'tbl_usr_outgoing';
}
