<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function promoType()
    {
        return $this->belongsTo(PromoType::class, 'promo_type_id');
    }
}
