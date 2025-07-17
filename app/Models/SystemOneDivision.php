<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemOneDivision extends Model
{
    protected $table = 'tbl_Division';
    protected $connection = 'mysql_systemone';
}
