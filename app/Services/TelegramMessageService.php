<?php

namespace App\Services;

use App\Traits\LoggerTrait;
use stdClass;

class TelegramMessageService
{
    use LoggerTrait;

    public function getMessage(array $source, ?object &$messageData = null, ?object &$callBackQuery = null)
    {
        $this->writeInfoLog('Got message from the telegram', [
            'source' => $source
        ]);

        $messageData = new stdClass();
        $from = new stdClass();
        $chat = new stdClass();

        $result = '';

        switch (true) {
            case isset($source['callback_query']):
                {
                    $callBackQuery = new stdClass();
                    $callBackQueryItem = $source['callback_query'];

                    $callBackQuery->id = $callBackQueryItem['id'];
                    $callBackQuery->chat_instance = $callBackQueryItem['chat_instance'];
                    $callBackQuery->data = $callBackQueryItem['data'];

                    $messageItem = $callBackQueryItem['message'];

                    $messageData->messageId = $messageItem['message_id'];

                    $from->id = $messageItem['from']['id'];
                    $from->isBot = $messageItem['from']['is_bot'];
                    $from->firstName = $messageItem['from']['first_name'];
                    $from->lastName = $messageItem['from']['last_name'] ?? null;
                    $from->userName = $messageItem['from']['username'] ?? null;

                    $callBackQuery->from = new stdClass();
                    $callBackQuery->from->id = $callBackQueryItem['from']['id'];
                    $callBackQuery->from->is_bot = $callBackQueryItem['from']['is_bot'];
                    $callBackQuery->from->firstName = $callBackQueryItem['from']['first_name'];
                    $callBackQuery->from->userName = $callBackQueryItem['from']['username'] ?? null;
                    $callBackQuery->from->languageCode = $callBackQueryItem['from']['language_code'];

                    $chat->id = $messageItem['chat']['id'];
                    $chat->firstName = $messageItem['chat']['first_name'];
                    $chat->userName = $messageItem['chat']['username'] ?? null;
                    $chat->type = $messageItem['chat']['type'];

                    $messageData->from = $from;
                    $messageData->chat = $chat;
                    $messageData->date = $messageItem['date'];
                    $messageData->text = $messageItem['text'];

                    $callBackQuery->message = $messageData;
                    $callBackQuery->replyMarkup = new stdClass();

                foreach ($callBackQueryItem['message']['reply_markup']['inline_keyboard'][0] as $key => $value) {
                    $callBackQuery->replyMarkup->inlineKeyboard[$key] = new stdClass();
                    $callBackQuery->replyMarkup->inlineKeyboard[$key]->text = $value['text'];
                    $callBackQuery->replyMarkup->inlineKeyboard[$key]->callbackData = $value['callback_data'];
                }
                    $result = $messageItem['text'];
            }
            break;
            case isset($source['message']):
                {
                    $messageItem = $source['message'];
                    $messageData->messageId = $messageItem['message_id'];
                    $messageData->date = $messageItem['date'];

                    $from->id = $messageItem['from']['id'];
                    $from->isBot = $messageItem['from']['is_bot'];
                    $from->firstName = $messageItem['from']['first_name'];
                    $from->lastName = $messageItem['from']['last_name'] ?? null;
                    $from->userName = $messageItem['from']['username'] ?? null;
                    $from->languageCode = $messageItem['from']['language_code'];

                    $chat->id = $messageItem['chat']['id'];
                    $chat->firstName = $messageItem['chat']['first_name'];
                    $chat->userName = $messageItem['chat']['username'] ?? null;
                    $chat->type = $messageItem['chat']['type'];

                    $messageData->from = $from;
                    $messageData->chat = $chat;

                    $result = isset($messageItem['text']) ? $messageItem['text'] : null;
            }
                break;
            case isset($source['edited_message']):
                {
                    $messageItem = $source['edited_message'];

                    $messageData->messageId = $messageItem['message_id'];
                    $messageData->date = $messageItem['date'];

                    $from->id = $messageItem['from']['id'];
                    $from->isBot = $messageItem['from']['is_bot'];
                    $from->firstName = $messageItem['from']['first_name'];
                    $from->lastName = $messageItem['from']['last_name'] ?? null;
                    $from->userName = $messageItem['from']['username'] ?? null;
                    $from->languageCode = $messageItem['from']['language_code'];

                    $chat->id = $messageItem['chat']['id'];
                    $chat->firstName = $messageItem['chat']['first_name'];
                    $chat->userName = $messageItem['chat']['username'];
                    $chat->type = $messageItem['chat']['type'];

                    $messageData->from = $from;
                    $messageData->chat = $chat;

                    $result = isset($messageItem['text']) ? $messageItem['text'] : null;
            }
                break;
            default:
                $this->writeErrorLog('Has not parsed message');
                throw new \Exception('Has not parsed message');
        }

        return $result;
    }
}
