<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function transactions()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function details()
    {
        return $this->hasMany(NoteDetail::class);
    }

    public function noteDetails()
    {
        return $this->hasMany(NoteDetail::class);
    }

    // Relasi dengan Transaction berdasarkan voucher_code
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'voucher_code', 'voucher_code');
    }

    // Relasi dengan Company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
