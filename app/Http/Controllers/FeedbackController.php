<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendFeedbackRequest;
use App\Services\FeedBackService;
use App\Services\TelegramService;

class FeedbackController extends Controller
{
    public function receive(FeedBackService $feedBackService, TelegramService $telegramService, SendFeedbackRequest $request)
    {
        return $feedBackService->sendMessageToAdminsTelegram($request, $telegramService);
    }

    public function getWriteFeedbackPage(FeedBackService $feedBackService)
    {
        return  $feedBackService->getWriteFeedbackPage();
    }
}
