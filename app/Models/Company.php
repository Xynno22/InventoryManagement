<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Company extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, MustVerifyEmailTrait;
    
  
    protected $fillable = [
        'name', 'email', 'password', 'address', 'phone','email_verified_at', 'verification_token'
    ];

    protected $hidden = [
        'password',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
