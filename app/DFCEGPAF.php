<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DFCEGPAF extends Model
{
    protected $connection = 'mysql_egpaf';
    public $table         = 'tbl_dfc_module';
}
