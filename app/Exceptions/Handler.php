<?php

namespace App\Exceptions;

use App\Services\Cache\ErrorUserMessageCacheService;
use App\Services\Cache\LanguageCacheService;
use App\Services\ResponseService;
use App\Services\TelegramService;
use App\Traits\LoggerTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Throwable;

class Handler extends ExceptionHandler
{
    use LoggerTrait;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     */
    public function register()
    {
        $this->renderable(function (\Exception $e, $request) {
            if ($request->is('api/*')) {
                $responseService = App::make(ResponseService::class);
                if(is_a($e, '\Illuminate\Auth\AuthenticationException')) {
                    return $responseService->errorResponse('Unauthenticated', 401);
                }
                elseif(is_a($e, '\Spatie\Permission\Exceptions\UnauthorizedException')) {
                    return $responseService->errorResponse($e->getMessage(), 403);
                } else {
                    return $responseService->errorResponse($e->getMessage(), 500);
                }
            }
        });

        $this->reportable(function (Throwable $e) {
            $isAllowedSentToTlg = true;
            switch (true) {
                case is_a($e, FailedTranslateException::class):
                    $isAllowedSentToTlg = false;
                    break;
            }

            $telegramService = App::make(TelegramService::class);
            $chatId = $telegramService->getChatId();

            if (!is_null($chatId)) {
                $errorUserMessageCacheService = App::make(ErrorUserMessageCacheService::class);
                $errorMessage = $errorUserMessageCacheService->getErrorMessageFromCache($chatId);

                $languageCacheService = App::make(LanguageCacheService::class);
                $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId($chatId);
                App::setLocale($userLanguage);

                if (is_null($errorMessage)) {
                    $errorUserMessageCacheService->setErrorMessageInCache($chatId, $e->getMessage());
                    $errorMessage = $errorUserMessageCacheService->getErrorMessageFromCache($chatId);

                    $telegramService->sendMessage(text: __('Sorry, something wrong happened with my program. I already reported it to the developers. I will be able to help you when they fix the program') .
                        hex2bin('F09F9895'));
                }

                if ($errorMessage != $e->getMessage()) {
                    $telegramService->sendMessage(text: __('Sorry, something wrong happened with my program. I already reported it to the developers. I will be able to help you when they fix the program') .
                        hex2bin('F09F9895'));
                }
            }

            $this->writeErrorLog('Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
            ], isAllowedSendToTlg: $isAllowedSentToTlg);
        });
        //return response()->json(['ok' => 'ok']);
    }
}
