<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetType extends Model
{
    protected $table = 'pet_types';
    protected $primaryKey = 'pet_type_id';

    public function breeds() {
        return $this->hasMany(PetBreed::class, 'pet_type_id', 'pet_type_id');
    }
}