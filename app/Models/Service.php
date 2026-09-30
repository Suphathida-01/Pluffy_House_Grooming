<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    // ตาราง services มีคอลัมน์ deleted_at -> ใช้ SoftDeletes (ลบแล้วแค่ซ่อน ไม่ได้ลบจริง)
    use SoftDeletes;

    // ชื่อตาราง (ตรงกับค่า default อยู่แล้ว แต่เขียนไว้ให้ชัด)
    protected $table = 'services';

    // ตารางนี้ใช้ service_id เป็น primary key ไม่ใช่ id -> ต้องบอก Laravel
    // (ถ้าลืมบรรทัดนี้ route model binding และ update() จะพัง)
    protected $primaryKey = 'service_id';

    // คอลัมน์ที่อนุญาตให้ใส่ผ่าน create() / update() ได้
    protected $fillable = [
        'service_name',
        'service_description',
        'service_duration_minutes',
        'service_price',      // คอลัมน์เดิมของ DB (NOT NULL) เราเก็บราคาขนาดเล็กไว้ที่นี่เป็นราคาเริ่มต้น
        'price_small',
        'price_medium',
        'price_large',
        'service_status',     // 'active' หรือ 'inactive'
    ];

    /**
     * ตัวช่วย: $service->is_active  -> true/false
     * (Laravel จับคู่ชื่อ getIsActiveAttribute -> is_active ให้อัตโนมัติ)
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->service_status === 'active';
    }

    
    public function priceForWeight(float $kg): float
    {
        if ($kg <= 7) {
            return (float) $this->price_small;
        }
        if ($kg <= 15) {
            return (float) $this->price_medium;
        }
        return (float) $this->price_large;
    }
}