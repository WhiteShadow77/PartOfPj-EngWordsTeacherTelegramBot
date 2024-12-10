<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryMessage extends Model
{
    protected $fillable = [
        'text',
        'arguments',
        'user_id',
        'type'
    ];
}
