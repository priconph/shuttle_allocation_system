<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Import Models here
 */
use App\Models\SystemOneHRIS;
use App\Models\SystemOneSubcon;
use App\Models\SystemOneDepartment;
use App\Models\SystemOneSection;
use App\Models\SystemOnePosition;
use App\Models\Routes;
use App\Models\RapidXUser;

class Masterlist extends Model
{
    public function hris_info(){
        return $this->hasOne(SystemOneHRIS::class, 'pkid', 'systemone_hris_id');
    }

    public function subcon_info(){
        return $this->hasOne(SystemOneSubcon::class, 'pkid', 'systemone_subcon_id');
    }

    public function routes_info(){
        return $this->hasOne(Routes::class, 'id', 'routes_id');
    }

    public function rapidx_user_info(){
        return $this->hasOne(RapidXUser::class, 'id', 'created_by');
    }
}
