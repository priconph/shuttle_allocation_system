<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Routes;

class RouteCode extends Model
{
    use HasFactory;

    protected $table = 'routes_code';
    protected $connection = 'mysql';

    public function routes_details(){
        return $this->hasMany(Routes::class, 'routes_description', 'routes_destination')->where('status', 1)->where('is_deleted', 0);
    }

}
