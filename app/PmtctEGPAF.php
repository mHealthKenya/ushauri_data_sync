<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PmtctEGPAF extends Model
{
    protected $connection = 'mysql_egpaf';
    public $table         = 'tbl_pmtct';
}
