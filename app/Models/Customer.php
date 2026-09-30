<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    protected $fillable = ['user_id'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function pets() {
        return $this->hasMany(Pet::class, 'customer_id', 'customer_id');
    }

    public function bookings() {
        return $this->hasMany(Booking::class, 'customer_id', 'customer_id');
    }
}
