<?php

namespace App\Providers;

use App\Services\Auth\AuthRedisService;
use App\Services\Cache\ErrorUserMessageCacheService;
use App\Services\Cache\LanguageCacheService;
use App\Services\Cache\QuizCacheService;
use App\Services\Cache\StudyWordCacheService;
use App\Services\CommandService;
use App\Services\DataStructures\TelegramCommandsFactory\WrongAnswer;
use App\Services\EnglishWordService;
use App\Services\Gallery\GalleryService;
use App\Services\GuestService;
use App\Services\Log\LogConfigService;
use App\Services\Log\LoggerService;
use App\Services\ResponseService;
use App\Services\StudyWordsService;
use App\Services\TelegramMessageService;
use App\Services\TelegramService;
use App\Services\UserService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Predis\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        $this->app->singleton( EnglishWordService::class, function(){
//            return new  EnglishWordService();
//        });

        $this->app->singleton(WrongAnswer::class, function () {
            return new  WrongAnswer(new QuizCacheService());
        });


        $this->app->singleton(QuizCacheService::class, function () {
            return new QuizCacheService();
        });

        $this->app->singleton(StudyWordCacheService::class, function () {
            return new StudyWordCacheService();
        });

        $this->app->singleton(LanguageCacheService::class, function () {
            return new LanguageCacheService();
        });

        $this->app->singleton(GuestService::class, function () {
            return new  GuestService();
        });

        $this->app->singleton(ResponseService::class, function () {
            return new ResponseService();
        });

        $this->app->singleton(GalleryService::class, function () {
            return new GalleryService();
        });

        $this->app->singleton(LoggerService::class, function () {
            return new LoggerService();
        });

        $this->app->singleton(LogConfigService::class, function () {
            return new LogConfigService();
        });



        $this->app->singleton(TelegramMessageService::class, function () {
            return new TelegramMessageService();
        });

        $this->app->singleton(CommandService::class, function () {
            return new CommandService();
        });

        $this->app->singleton(EnglishWordService::class, function () {
            return new EnglishWordService();
        });

        $this->app->singleton(UserService::class, function () {
            return new UserService($this->app->make(EnglishWordService::class));
        });

        $this->app->singleton(StudyWordsService::class, function () {
            return new StudyWordsService();
        });

        $this->app->singleton(AuthRedisService::class, function () {
            return new AuthRedisService();
        });

        $this->app->singleton(TelegramService::class, function () {
            return new TelegramService(
                $this->app->make(TelegramMessageService::class),
                $this->app->make(CommandService::class),
                $this->app->make(UserService::class),
                $this->app->make(StudyWordsService::class),
                $this->app->make(EnglishWordService::class),
                $this->app->make(AuthRedisService::class)
            );
        });

        $this->app->singleton(ErrorUserMessageCacheService::class, function () {
            return new ErrorUserMessageCacheService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });
        Paginator::useBootstrap();
    }
}
