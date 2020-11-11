<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PmtctFaces extends Model
{
    protected $connection = 'mysql_faces';
    public $table         = 'tbl_pmtct';
}
