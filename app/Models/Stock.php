<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    public $timestamps = false;

    protected $fillable = [
        'productID',
        'currentStock',
        'minimumStock',
        'lastUpdated',
        'companyID',
        'totalOrder'
    ];
    protected $casts = [
        'lastUpdated' => 'datetime',
    ];
    // Relasi dengan Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'productID');
    }

    // Relasi dengan User (yang mengupdate stok)
    public function company()
    {
        return $this->belongsTo(Company::class, 'companyID');
    }
}
