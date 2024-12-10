<?php

return [
    'required_max_variants' => env('REQUIRED_MAX_QUIZ_VARIANTS', 8),
    'required_min_variants' => env('REQUIRED_MIN_QUIZ_VARIANTS', 4),
    'user_answer_waiting_seconds' => env('QUIZ_USER_ANSWER_WAITING_SECONDS', 30),
    'available_quantity' => env('QUIZ_AVAILABLE_QUANTITY', 10),

    'schedule_sending_days_template' => [
        'Mon' => 1,
        'Tue' => 2,
        'Wed' => 4,
        'Thu' => 8,
        'Fri' => 16,
        'Sat' => 32,
        'Sun' => 128
    ],
    'schedule_sending_times_template' => [
        '07:00',
        '08:00',
        '09:00',
        '10:00',
        '11:00',
        '12:00',
        '13:00',
        '14:00',
        '15:00',
        '16:00',
        '17:00',
        '18:00',
        '19:00',
        '20:00',
        '21:00',
        '22:00'
    ],
    'repeat_known_words_percents_set' => [1, 2, 3, 4, 5, 10, 15, 25]
];
