<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtherFnlOutcomeEGPAF extends Model
{
    protected $connection = 'mysql_egpaf';
    public $table         = 'tbl_other_final_outcome';
}
