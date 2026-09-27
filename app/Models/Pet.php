<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model
{
    use SoftDeletes;

    protected $table = 'pets';
    protected $primaryKey = 'pet_id';

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function breed() {
        return $this->belongsTo(PetBreed::class, 'pet_breed_id', 'pet_breed_id');
    }

    public function bookings() {
        return $this->hasMany(Booking::class, 'pet_id', 'pet_id');
    }
}
