<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';

     protected $fillable = [
        'booking_date',
        'booking_start_time',
        'booking_end_time',
        'booking_total_price',
        'booking_status',   // 'pending' | 'confirmed' | 'completed'
        'customer_id',
        'admin_id',         // ช่างที่รับงาน
        'service_id',
        'pet_id',
    ];

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function pet() {
        return $this->belongsTo(Pet::class, 'pet_id', 'id');
    }

    public function service() {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function admin() {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    public function payment() {
        return $this->hasOne(Payment::class, 'booking_id', 'booking_id');
    }
}
