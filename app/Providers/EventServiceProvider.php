<?php

namespace App\Providers;

use App\Events\EnglishWordTranslateEvent;
use App\Events\FailedJobExceptionEvent;
use App\Events\StudyWordTranslateEvent;
use App\Events\UntranslatedWordEvent;
use App\Events\UntranslatedStudyWordEvent;
use App\Events\UntranslatedWordByIdEvent;
use App\Listeners\EnglishWordTranslateEventsListener;
use App\Listeners\FailedJobExceptionEventsListener;
use App\Listeners\StudyWordTranslateEventsListener;
use App\Listeners\UntranslatedWordEventsListener;
use App\Listeners\UntranslatedStudyWordEventsListener;
use App\Listeners\UntranslatedWordByIdEventsListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UntranslatedWordEvent::class => [
            UntranslatedWordEventsListener::class
        ],
        EnglishWordTranslateEvent::class => [
            EnglishWordTranslateEventsListener::class
        ],
        FailedJobExceptionEvent::class => [
            FailedJobExceptionEventsListener::class
        ],
        UntranslatedStudyWordEvent::class => [
            UntranslatedStudyWordEventsListener::class
        ],
        StudyWordTranslateEvent::class => [
            StudyWordTranslateEventsListener::class
        ],
        UntranslatedWordByIdEvent::class => [
            UntranslatedWordByIdEventsListener::class
        ]
    ];

    protected $subscribe = [
        //UntranslatedWordEventsListener::class
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
