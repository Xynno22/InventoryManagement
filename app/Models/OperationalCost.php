<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'amount',
        'note',
        'company_id'
    ];
    public function company()
    {
        return $this->belongsTo(Company::class, 'companyID');
    }
}