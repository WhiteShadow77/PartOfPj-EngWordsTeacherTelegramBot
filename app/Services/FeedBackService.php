<?php

namespace App\Services;

use Illuminate\Http\Request;

class FeedBackService
{
    public function sendMessageToAdminsTelegram(Request $request, TelegramService $telegramService)
    {
        $text = 'Feedback received:' . PHP_EOL .
            'Name: ' . $request->contact_name . PHP_EOL .
            'E-mail: ' . $request->contact_email . PHP_EOL .
            $request->contact_message . PHP_EOL .
            '#feedback';

        $telegramService->sendMessageToAdmin($text);

        return redirect()->back();
    }

    public function getWriteFeedbackPage()
    {
        return view('guest.write-feedback-page');
    }
}
