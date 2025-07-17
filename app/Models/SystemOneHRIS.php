<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Import Models here
 */
use App\Models\SystemOnePosition;
use App\Models\SystemOneDivision;
use App\Models\SystemOneDepartment;
use App\Models\SystemOneSection;

class SystemOneHRIS extends Model
{
    protected $table = 'tbl_EmployeeInfo';
    protected $connection = 'mysql_systemone';

    public function position_info(){
        return $this->hasOne(SystemOnePosition::class, 'pkid', 'fkPosition');
    }
    
    public function division_info(){
        return $this->hasOne(SystemOneDivision::class, 'pkid', 'fkDivision');
    }

    public function department_info(){
        return $this->hasOne(SystemOneDepartment::class, 'pkid', 'fkDepartment');
    }
    
    public function section_info(){
        return $this->hasOne(SystemOneSection::class, 'pkid', 'fkSection');
    }

    

    
}
