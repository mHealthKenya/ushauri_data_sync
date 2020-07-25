<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserEGPAF extends Model
{
    protected $connection = 'mysql_egpaf';
    public $table         = 'tbl_users';
}
