<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientOutgoingFaces extends Model
{
    protected $connection = 'mysql_faces';
    public $table         = 'tbl_clnt_outgoing';
}
