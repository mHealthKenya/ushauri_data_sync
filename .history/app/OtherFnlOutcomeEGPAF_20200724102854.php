<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtherFnlOutcomeFaces extends Model
{
    protected $connection = 'mysql_faces';
    public $table         = 'tbl_other_final_outcome';
}
