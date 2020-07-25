<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFaces extends Model
{
 protected $connection = 'mysql_faces';
 public $table         = 'tbl_users';
}
