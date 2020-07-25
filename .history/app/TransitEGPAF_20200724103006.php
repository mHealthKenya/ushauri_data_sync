<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransitFaces extends Model
{
    protected $connection = 'mysql_faces';
    public $table         = 'tbl_transit_app';
}
