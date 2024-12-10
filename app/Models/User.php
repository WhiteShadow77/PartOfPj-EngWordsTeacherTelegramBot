<?php

namespace App\Models;

use App\Enums\SentWordsKind;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'language_code',
        'type',
        'telegram_user_name',
        'chat_id',
        'telegram_user_id',
        'eng_words_per_twitch',
        'photo_url',
        'remember_token',
        'is_enabled_english_words_sending',
        'is_enabled_quiz_sending',
        'english_words_week_sending_conf',
        'english_words_week_and_times_sending_conf',
        'quiz_week_sending_conf',
        'quiz_week_and_times_sending_conf',
        'quiz_max_answers',
        'quiz_quantity_sending_conf',
        'is_enabled_repeat_already_known_in_quiz',
        'repeat_known_words_percent_in_quiz',
        'language'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions'          => 'array',
        'email_verified_at'    => 'datetime',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'id',
        'name',
        'email',
        'permissions',
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'email',
        'updated_at',
        'created_at'
    ];

//    public function studyWords(): HasMany
//    {
//        return $this->hasMany(StudyWord::class);
//    }

    public function historyMessages(): HasMany
    {
        return $this->hasMany(HistoryMessage::class);
    }

    public function sentWords()
    {
        return $this->belongsToMany(
            EnglishWord::class,
            'sent_words',
            'user_id',
            'english_word_id'
        )->withTimestamps();
    }

    public function studyWords()
    {
        return $this->belongsToMany(
            EnglishWord::class,
            'study_words',
            'user_id',
            'english_word_id'
        )->withTimestamps();
    }

    public function sentStudyWords()
    {
        return $this->belongsToMany(
            EnglishWord::class,
            'sent_study_words',
            'user_id',
            'english_word_id'
        )->withTimestamps();
    }

    public function untranslatedStudyWords()
    {
        return $this->belongsToMany(
            EnglishWord::class,
            'untranslated_study_words_m',
            'user_id',
            'english_word_id'
        )->withTimestamps();
    }

    public function knownWords()
    {
        return $this->belongsToMany(
            EnglishWord::class,
            'known_words',
            'user_id',
            'english_word_id'
        )->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
