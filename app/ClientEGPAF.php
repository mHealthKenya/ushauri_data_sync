<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientEGPAF extends Model
{
    protected $connection = 'mysql_egpaf';
    public $table         = 'tbl_client';
}
