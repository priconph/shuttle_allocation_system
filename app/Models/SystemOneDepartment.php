<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemOneDepartment extends Model
{
    protected $table = 'tbl_Department';
    protected $connection = 'mysql_systemone';
}
