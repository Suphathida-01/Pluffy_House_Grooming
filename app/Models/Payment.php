<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    public function booking() {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
