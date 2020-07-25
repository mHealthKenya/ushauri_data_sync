<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BroadcastFaces extends Model
{
    protected $connection = 'mysql_faces';
    public $table         = 'tbl_broadcast';
}
