<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{
    use SoftDeletes;

    protected $table = 'admins';
    protected $primaryKey = 'admin_id';

    protected $fillable = ['user_id'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function schedules() {
        return $this->hasMany(AdminSchedule::class, 'admin_id', 'admin_id');
    }

    public function bookings() {
        return $this->hasMany(Booking::class, 'admin_id', 'admin_id');
    }
}