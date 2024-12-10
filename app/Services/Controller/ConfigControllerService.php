<?php

namespace App\Services\Controller;

use App\Services\UserService;

class ConfigControllerService
{
    public function getConfigTemplate(UserService $userService)
    {
        return view('home.config', [
            'currentEnglishWordsPortion' => $userService->getCurrentEnglishWordsQuantity(),
            'minEnglishWordsPortion' => config('english_words.portion.min'),
            'maxEnglishWordsPortion' => config('english_words.portion.max'),
            'engWordsSendingTimesList' => config('english_words.schedule_sending_times_template'),
            'quizSendingTimesList' => config('quiz.schedule_sending_times_template'),
            'quizMinVariantQuantity' => config('quiz.required_min_variants'),
            'quizMaxVariantQuantity' => config('quiz.required_max_variants'),
            'currentQuizMaxAnswers' => $userService->getCurrentQuizAnswersQuantity(),
            'quizAvailableQuantity' => config('quiz.available_quantity'),
            'repeatKnownWordsPercentsSet' => config('quiz.repeat_known_words_percents_set'),
            'languages' => config('language.for_config_page'),
            'currentLanguage' => $userService->getCurrentLanguage()
        ]);
    }
}
