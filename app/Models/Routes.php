<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Import Models here
 */
use App\Models\PickupTime;
use App\Models\ShuttleProvider;

class Routes extends Model
{
    use HasFactory;

    public function pickup_time_info(){
        return $this->hasOne(PickupTime::class, 'id', 'pickup_time_id');
    }

    public function shuttle_provider_info(){
        return $this->hasOne(ShuttleProvider::class, 'id', 'shuttle_provider_id');
    }
}
