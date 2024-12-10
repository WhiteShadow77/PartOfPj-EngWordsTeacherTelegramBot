<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryRecord extends Model
{
    protected $fillable = [
        'word',
        'answer_kind',
        'word_status',
        'word_kind',
        'right_word',
        'word_id',
        'user_id'
    ];
}
