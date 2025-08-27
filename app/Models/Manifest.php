<?php

namespace App\Models;

use App\Models\RouteCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Manifest extends Model
{
    use HasFactory;

    public function route_details(){
        return $this->hasOne(RouteCode::class, 'routes_code', 'route');
    }
}
