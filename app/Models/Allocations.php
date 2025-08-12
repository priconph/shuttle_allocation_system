<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Routes;
use App\Models\Masterlist;
use App\Models\RapidXUser;

class Allocations extends Model
{
    use HasFactory;

    // public function requested_by_info(){
    //     return $this->hasOne(Masterlist::class, 'id', 'requestor_ml_id');
    // }

    public function request_ml_info(){
        return $this->hasOne(Masterlist::class, 'id', 'requestee_ml_id');
    }

    public function requestor_user_info(){
        return $this->hasOne(RapidXUser::class, 'id', 'requested_by');
    }

    public function alloc_route_info(){
        return $this->hasOne(Routes::class, 'id', 'alloc_routes_id');
    }

    public function routes_info(){
        return $this->hasOne(Routes::class, 'id', 'alloc_routes_id');
    }

}
