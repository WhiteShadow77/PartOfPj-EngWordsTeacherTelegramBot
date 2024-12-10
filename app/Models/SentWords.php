<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SentWords extends Model
{
    protected $fillable = [
        'english_word_id',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function englishWord()
    {
        return $this->belongsTo(EnglishWord::class);
    }
}
