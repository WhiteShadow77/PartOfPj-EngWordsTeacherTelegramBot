<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnownWordsId extends Model
{
    protected $fillable = [
        'english_word_id',
        'user_id',
    ];
}
