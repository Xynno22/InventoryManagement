<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function promos()
    {
        return $this->hasMany(Promo::class);
    }
}
