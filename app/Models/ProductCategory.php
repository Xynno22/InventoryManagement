<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company_id'];

    public function company()
    {
        //Menandakan bahwa setiap kategori dimiliki oleh satu perusahaan.
        return $this->belongsTo(Company::class);
    }
}
