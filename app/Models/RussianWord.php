<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RussianWord
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EnglishWord[] $englishWords
 * @property-read int|null $english_words_count
 * @method static \Illuminate\Database\Eloquent\Builder|RussianWord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RussianWord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RussianWord query()
 * @mixin \Eloquent
 */
class RussianWord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'word',
        'created_at',
        'updated_at',
    ];

    public function englishWords()
    {
        return $this->belongsToMany(EnglishWord::class)->withTimestamps();
    }
}
