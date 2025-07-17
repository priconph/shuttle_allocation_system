<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemOnePosition extends Model
{
    protected $table = 'tbl_Position';
    protected $connection = 'mysql_systemone';
}
