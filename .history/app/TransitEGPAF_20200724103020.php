<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransitEGPAF extends Model
{
    protected $connection = 'mysql_egpaf';
    public $table         = 'tbl_transit_app';
}
