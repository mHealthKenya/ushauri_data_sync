<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientOutgoingEGPAF extends Model
{
    protected $connection = 'mysql_egpaf';
    public $table         = 'tbl_clnt_outgoing';
}
