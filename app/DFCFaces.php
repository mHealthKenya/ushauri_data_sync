<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DFCFaces extends Model
{
    protected $connection = 'mysql_faces';
    public $table         = 'tbl_dfc_module';
}
