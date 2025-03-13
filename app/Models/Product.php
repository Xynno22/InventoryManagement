<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'categoryID',
        'detail',
        'image',
        'unit',
        'companyID',
        'location',
        'purchase_price',
        'sale_price',
    ];

    /**
     * Relationship: A product belongs to a category
     */
    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class, 'categoryID');
    }

    /**
     * Relationship: A product belongs to a user (who added it)
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'companyID');
    }

    /**
     * Accessor: Format price to 2 decimal places
     */
    public function getBuyingPriceAttribute($value)
    {
        return number_format($value, 2, '.', ',');
    }

    public function getSellingPriceAttribute($value)
    {
        return number_format($value, 2, '.', ',');
    }

    /**
     * Mutator: Ensure price is stored as float
     */
    public function setBuyingPriceAttribute($value)
    {
        $this->attributes['purchase_price'] = floatval($value);
    }

    public function setSellingPriceAttribute($value)
    {
        $this->attributes['sale_price'] = floatval($value);
    }
}
