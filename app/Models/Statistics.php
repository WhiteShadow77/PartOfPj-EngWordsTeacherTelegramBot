<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistics extends Model
{
    protected $fillable = [
        'user_id',
        'word_status',
        'english_word_id'
    ];

    protected $dateFormat = 'Y-m-d';
}
