<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CareGiverEGPAF extends Model
{
    protected $connection = 'mysql_egpaf';
    public $table         = 'tbl_caregiver_not_on_care';
}
