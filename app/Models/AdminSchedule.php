<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSchedule extends Model
{
    protected $table = 'admin_schedules';
    protected $primaryKey = 'schedule_id';

    public function admin() {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }
}