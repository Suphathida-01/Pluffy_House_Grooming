<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetType extends Model
{
    protected $table = 'pet_types';
    protected $primaryKey = 'pet_type_id';

    // ประเภทสัตว์ 1 ชนิด มีได้หลายสายพันธุ์
    public function breeds() {
        return $this->hasMany(PetBreed::class, 'pet_type_id', 'pet_type_id');
    }
}