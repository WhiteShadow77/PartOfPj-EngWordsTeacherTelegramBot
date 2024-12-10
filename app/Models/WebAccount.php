<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebAccount extends User implements Authenticatable
{
    use HasFactory;

    protected $table = 'web_accounts';

    protected $fillable = [
        'name',
        'email',
        'password',
        'telegram_user_id',
        'user_id',
        'remember_token',
        'email_verified_at'
    ];
}
