<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientNascop extends Model
{
    protected $connection = 'mysql_nascop';
    public $table         = 'tbl_client';
}
