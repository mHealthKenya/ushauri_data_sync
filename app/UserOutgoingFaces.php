<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserOutgoingFaces extends Model
{
    protected $connection = 'mysql_faces';
    public $table         = 'tbl_usr_outgoing';
}
