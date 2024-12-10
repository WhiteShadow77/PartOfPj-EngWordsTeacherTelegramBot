<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UntranslatedStudyWords extends Model
{
    public string $table = 'untranslated_study_words_m';

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
