<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CareGiverFaces extends Model
{
    protected $connection = 'mysql_faces';
    public $table         = 'tbl_caregiver_not_on_care';
}
