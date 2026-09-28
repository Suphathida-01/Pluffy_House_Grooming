<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function pet() {
        return $this->belongsTo(Pet::class, 'pet_id', 'pet_id');
    }

    public function service() {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function admin() {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    // การจอง 1 รายการ มีข้อมูลการชำระเงิน 1 รายการ
    public function payment() {
        return $this->hasOne(Payment::class, 'booking_id', 'booking_id');
    }
}
