<?php

namespace App\Http\Middleware;

use App\Services\Auth\AuthRedisService;
use App\Services\TelegramMessageService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Services\Cache\LanguageCacheService;
use App\Traits\LoggerTrait;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class AuthTelegramRequestMiddleware
{
    use LoggerTrait;

    public function __construct(
        private readonly UserService $userService,
        private readonly TelegramMessageService $telegramMessageService,
        private readonly TelegramService $telegramService,
        private readonly AuthRedisService $authRedisService,
        private readonly LanguageCacheService $languageCacheService
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse|RedirectResponse|Response|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $message = $this->telegramMessageService->getMessage($request->all(), $messageData, $callbackData);
        $isCallback = !is_null($callbackData);
        if ($isCallback) {
            $messageData = $callbackData;
        }
        $this->writeInfoLog('Before authorization request data', [
            'is callback using' => $isCallback,
            'from' => [
                'id' => $isCallback ? $callbackData->from->id : $messageData->from->id,
                'username' => $isCallback ? $callbackData->from->userName : $messageData->from->userName,
            ]
        ]);

        $isUserAuthorized = $this->authRedisService->authorize($messageData->from->id, $this->userService);

        if ($isUserAuthorized && $message != '/start') {
            $this->writeInfoLog('Successfully authorized', [
                'from' => [
                    'username' => $messageData->from->userName
                ]
            ]);
            return $next($request);
        }
        if ($isUserAuthorized && $message == '/start') {
            $this->writeInfoLog('User has already registered', [
                'from' => [
                    'username' => $messageData->from->userName
                ]
            ]);
            $userLanguage = $this->languageCacheService->getLanguageInsteadFromDbByChatId($messageData->chat->id);
            App::setLocale($userLanguage);
            $this->telegramService->sendMessage(
                $messageData->chat->id,
                __("I already know you") . ', ' . $messageData->from->firstName . hex2bin('F09F988A')
            );
            return response()->json(['ok' => 'ok']);
        }
        if (!$isUserAuthorized && $message != '/start') {
            $this->writeInfoLog('Not authorized', [
                'from' => [
                    'username' => $messageData->from->userName
                ]
            ]);
            $this->telegramService->sendMessage(
                $messageData->chat->id,
                'Извините, я не знаю кто Вы. Начните работу со сной с команды /start'
            );
            return response()->json(['ok' => 'ok']);
        }
        if (!$isUserAuthorized && $message == '/start') {
            $this->writeInfoLog('Successfully registered', [
                'from' => [
                    'username' => $messageData->from->userName
                ]
            ]);
            return $next($request);
        }
    }
}
