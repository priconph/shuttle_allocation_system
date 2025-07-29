<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubconAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'emp_id',
        'emp_name',
        'date_in',
        'time_in',
        'date_out',
        'time_out',
        'created_by',
    ];
}
