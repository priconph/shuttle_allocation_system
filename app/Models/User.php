<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

/**
 * Import Models here
 */
use App\Models\UserLevel;
use App\Models\UserRole;
use App\Models\RapidXUser;
use App\Models\Masterlist;

class User extends Authenticatable // Authenticatable this will allow the use of Auth::user()
{
    public function user_levels(){
        return $this->hasOne(UserLevel::class, 'id', 'user_level_id');
    }

    public function user_roles(){
        return $this->hasOne(UserRole::class, 'id', 'user_role_id');
    }

    public function rapidx_user_info(){
        return $this->hasOne(RapidXUser::class, 'id', 'rapidx_user_id');
    }

    public function master_list_info(){
        return $this->hasOne(Masterlist::class, 'masterlist_employee_number', 'employee_number');
    }
}
