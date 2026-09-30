<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ตารางงานของช่าง (วันไหน เวลาไหน เข้างานหรือไม่)
 * ตาราง admin_schedules ไม่มี deleted_at -> ไม่ต้องใช้ SoftDeletes
 */
class AdminSchedule extends Model
{
    protected $table = 'admin_schedules';
    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'work_date',    // เช่น 2026-09-28
        'start_time',   // เช่น 09:00
        'end_time',     // เช่น 17:00
        'status',       // 'available' = เข้างาน | 'unavailable' = หยุด/ลา
        'admin_id',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }
}