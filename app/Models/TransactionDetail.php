<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $guarded = ['id'];

    // Relasi dengan Transaction
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relasi dengan Product (jika diperlukan)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }
}
