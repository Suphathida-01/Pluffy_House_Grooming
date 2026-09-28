<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $table = 'services';
    protected $primaryKey = 'service_id';

    public function bookings() {
        return $this->hasMany(Booking::class, 'service_id', 'service_id');
    }
}