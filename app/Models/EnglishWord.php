<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EnglishWord
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RussianWord[] $russianWords
 * @property-read int|null $russian_words_count
 * @method static \Illuminate\Database\Eloquent\Builder|EnglishWord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnglishWord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnglishWord query()
 * @mixin \Eloquent
 */
class EnglishWord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'word',
        'transcription',
        'uk_pron_file',
        'us_pron_file',
        'created_at',
        'updated_at',
    ];

    public function russianWords()
    {
        return $this->belongsToMany(RussianWord::class);
    }
}
