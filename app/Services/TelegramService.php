<?php

namespace App\Services;

use App\Services\Auth\AuthRedisService;
use App\Services\Cache\LanguageCacheService;
use App\Services\DataStructures\TelegramCommandsFactory\TelegramCommandFactory;
use App\Services\History\HistoryRecordService;
use App\Services\History\HistoryMessageService;
use App\Traits\LoggerTrait;
use CURLFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class TelegramService
{
    use LoggerTrait;

    private string $uri;
    private string $chatId;

    private TelegramMessageService $telegramMessageService;
    private CommandService $commandService;
    private UserService $userService;
    private StudyWordsService $studyWordsService;
    private EnglishWordService $englishWordService;
    private AuthRedisService $authRedisService;

    public function __construct(
        TelegramMessageService $telegramMessageService,
        CommandService $commandService,
        UserService $userService,
        StudyWordsService $studyWordsService,
        EnglishWordService $englishWordService,
        AuthRedisService $authRedisService,
    ) {
        $this->uri = config('bot.send_message_to_bot_uri');
        $this->telegramMessageService = $telegramMessageService;
        $this->commandService = $commandService;
        $this->userService = $userService;
        $this->studyWordsService = $studyWordsService;
        $this->englishWordService = $englishWordService;
        $this->authRedisService = $authRedisService;
    }

    /** Sends the message to telegram.
     *
     * @param ?string $chatId = null
     * @param ?string $text = null
     * @return bool
     */
    public function sendMessage(?string $chatId = null, ?string $text = null): bool
    {
        $this->writeInfoLog('Sending message to telegram', [
            'message' => $text,
            'chat_id' => $chatId ?? $this->chatId
        ]);
        if ($text != '' && !is_null($text)) {
            $request = [
                "chat_id" => $chatId ?? $this->chatId,
                "text" => $text,
                "parse_mode" => "markdown",
            ];

            $response = Http::get($this->uri, $request);
            $this->writeInfoLog('Sending message to telegram. Response', [
                'request' => $request,
                'response' => $this->logResponseHelper($response)
            ]);
            return $response->ok();
        } else {
            $this->writeErrorLog('Error sending message to telegram. Empty message', [
                'message' => $text,
                "chat_id" => $chatId ?? $this->chatId,
            ]);
            return false;
        }
    }

    /** Handles the received message.
     *
     * @param Request $request
     * @param HistoryRecordService $historyRecordService
     * @param HistoryMessageService $messageRecordService
     * @param LanguageCacheService $languageCacheService
     * @return JsonResponse
     */
    public function receiveMessage(
        Request $request,
        HistoryRecordService $historyRecordService,
        HistoryMessageService $messageRecordService,
        LanguageCacheService $languageCacheService
    ): JsonResponse {
        /** @var $messageData */
        /** @var $callBackData */
        /** @var $arguments */

        $message = $this->telegramMessageService->getMessage($request->all(), $messageData, $callBackData);
        $this->writeInfoLog('Got message data', [
            'message data' => $messageData,
            'LanguageCacheService obj id' => spl_object_id($languageCacheService)
        ]);
        $this->chatId = $messageData->chat->id;

        $command = $this->commandService->parse($message, $arguments);
        if ($command !== false) {
            $commandInstance = TelegramCommandFactory::createCommand(
                $command,
                $arguments,
                commandNamesWithoutArgQuantityLimit: ['comment']
            );
            if ($commandInstance !== false) {
                return $commandInstance->run(
                    $this->userService,
                    $historyRecordService,
                    $messageRecordService,
                    $messageData,
                    $this->authRedisService,
                    $this,
                    $this->englishWordService,
                    $this->chatId,
                    $languageCacheService
                );
            } else {
                $this->answerToReply(
                    $messageData->chat->id,
                    TelegramCommandFactory::$errorUserMessage,
                    $messageData->messageId
                );
                return response()->json(['ok' => TelegramCommandFactory::$errorResponseMessage]);
            }
        } else {
            if (!is_null($callBackData)) {
                $command = $this->commandService->parse($callBackData->data, $arguments);
                $callBackDataItems = explode(' ', $callBackData->data);
                next($callBackDataItems);
                $rightAnswerWord = current($callBackDataItems);
                next($callBackDataItems);
                $buttonChosenVariant = current($callBackDataItems);

                $arguments[1] = $rightAnswerWord;
                $arguments[2] = $buttonChosenVariant;

                $messageId = $messageData->messageId;

                $commandInstance = TelegramCommandFactory::createCommand($command, $arguments, $messageId);
                if ($commandInstance !== false) {
                    return $commandInstance->run(
                        $this->userService,
                        $historyRecordService,
                        $messageRecordService,
                        $callBackData,
                        $this->authRedisService,
                        $this,
                        $this->englishWordService,
                        $this->chatId,
                        $languageCacheService
                    );
                } else {
                    $this->answerToReply(
                        $messageData->chat->id,
                        TelegramCommandFactory::$errorUserMessage,
                        $messageData->messageId
                    );
                    return response()->json(['ok' => TelegramCommandFactory::$errorResponseMessage]);
                }
            } else {
                $this->answerToReply($messageData->chat->id, 'Неправильная команда', $messageData->messageId);
                return response()->json(['ok' => 'Wrong command']);
            }
        }
    }

    /** Answer directly for a User reply.
     *
     * @param string $chatId
     * @param string $message
     * @param string $replyToMessageId
     * @return void
     */
    public function answerToReply(string $chatId, string $message, string $replyToMessageId): void
    {
        $request = [
            'chat_id' => $chatId,
            'text' => $message,
            'reply_to_message_id' => $replyToMessageId
        ];

        $response = Http::get($this->uri, $request);

        $this->writeInfoLog('Sending message to telegram. Response', [
            'request' => $request,
            'response' => $this->logResponseHelper($response)
        ]);
    }

    /** Sends message and buttons to telegram.
     *
     * @param string $chatId
     * @param string $text
     * @param array $buttonsStruct
     * @param int|null $sentMessageId
     * @return bool
     */
    public function sendMessageAndButtons(
        string $chatId,
        string $text,
        array $buttonsStruct = [
            [
                'text' => 'Button 1',
                'callback_data' => 'pressed_button_1',
            ],

            [
                'text' => 'Button 2',
                'callback_data' => 'pressed_button_2',
            ],
        ],
        ?int &$sentMessageId = null
    ): bool {
        $this->writeInfoLog('Sending message and buttons to telegram', [
            'message' => $text,
            'chat_id' => $chatId,
            'buttons struct ' => $buttonsStruct
        ]);
        if ($text != '') {
            $request = [
                'chat_id' => $chatId,
                'text' => $text,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $buttonsStruct,
                ]),
            ];

            $response = Http::get($this->uri, $request);

            $this->writeInfoLog(
                'Sending message and buttons to telegram. Response',
                $this->logResponseHelper($response, $sentMessageId)
            );
            return $response->ok();
        } else {
            $this->writeErrorLog('Error sending message to telegram. Empty message', [
                'message' => $text,
                'buttons struct' => $buttonsStruct
            ]);
            return false;
        }
    }

    /** Sends the url to telegram.
     *
     * @param string $urlText
     * @param string $url
     * @param string $chatId
     * @param string|null $textBeforeUrl
     */
    public function sendUrl(string $urlText, string $url, string $chatId, ?string $textBeforeUrl = null): void
    {
        $this->sendMessage($chatId, $textBeforeUrl ?? '' . '[' . $urlText . '](' . $url . ')');
    }

    public function sendMp3File(
        ?string $fileDirAndName,
        string $chatId,
        ?string $title = null,
        ?string $performer = null,
        ?string $caption = null
    ): void {
        $this->writeInfoLog('Sending mp3 file to telegram', [
            'file name and dir' => $fileDirAndName,
            'title' => $title,
            'performer' => $performer,
            'caption' => $caption,
            'chat id' => $chatId
        ]);

        if ($fileDirAndName != '' && !str_starts_with($fileDirAndName, 'fake')) {
            $cfile = new CURLFile(realpath($fileDirAndName));
            $data = [
                'chat_id' => $chatId,
                'audio' => $cfile,
                'title' => $title,
                'performer' => $performer,
                'caption' => $caption,
                //'duration' => 22 //seconds
            ];
            $ch = curl_init(config('bot.uri_and_token') . '/' . 'sendAudio');
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_exec($ch);
            curl_close($ch);
        } else {
            $this->writeInfoLog('Skipped sending mp3 file to telegram. No file to send');
        }
    }

    /** Sends sveral mp3 files to telegram.
     *
     * @param array $fileDirNameAndChatIdAndTitleAndPerformerAndCationItems
     * @param string $chatId
     */
    public function sendSeveralMp3Files(array $fileDirNameAndChatIdAndTitleAndPerformerAndCationItems, string $chatId): void
    {
        foreach ($fileDirNameAndChatIdAndTitleAndPerformerAndCationItems as $item) {
            $this->sendMp3File(
                $item[0],
                $chatId,
                $item[1] ?? null,
                $item[2] ?? null,
                $item[3] ?? null,
            );
        }
    }

    /** Disables all buttons using sent message's callbacks.
     *
     * @param string $chatId
     * @param string $messageId
     * @param array $buttonsStruct
     * @param bool $setNoEntrySmileToButtons
     * @return mixed
     */
    public function disableAllButtonsCallbackOfMessage(
        string $chatId,
        string $messageId,
        array $buttonsStruct,
        bool $setNoEntrySmileToButtons = true
    ) {
        $this->writeInfoLog('disableAllButtonsCallbackOfMessage method executing', [
            'chat id' => $chatId,
            'message id' => $messageId,
            "butons struct before" => $buttonsStruct,
            'set no entry smile to buttons' => $setNoEntrySmileToButtons,
        ]);

        array_walk_recursive($buttonsStruct, function (&$item, $key) use ($setNoEntrySmileToButtons) {

            if ($key == 'text' && $setNoEntrySmileToButtons == true) {
                $item = hex2bin('F09F9AAB') . $item;
            }

            if ($key == 'callback_data') {
                $item = '#disabledButtonPress';
            }
        });

        $this->writeInfoLog('disableAllButtonsCallbackOfMessage method executing', [
            "butons struct after" => $buttonsStruct,
            'set no entry smile to buttons' => $setNoEntrySmileToButtons,
        ]);

        return $this->sendEditButtonsRequest($chatId, $messageId, $buttonsStruct);
    }

    /** Disables all buttons using sent message's callbacks and markups right answer.
     *
     * @param string $chatId
     * @param string $messageId
     * @param array $buttonsStruct
     * @return mixed
     */
    public function disableButtonsCallbackOfMessageSetRightAnswer(
        string $chatId,
        string $messageId,
        ?array $buttonsStruct
    ) {
        if (is_null($buttonsStruct)) {
            $buttonsStruct = [];
        }
        $this->writeInfoLog('disableButtonsCallbackOfMessageSetRightAnswer method executing', [
            "butons struct before" => $buttonsStruct,
        ]);

        $i = 0;
        $buffer = [];
        array_walk_recursive($buttonsStruct, function (&$item, $key) use (&$buffer, &$i) {

            if ($key == 'text') {
                $buffer[$i++] = $item;
            }

            if ($key == 'callback_data' && str_contains($item, '#rightAnswer')) {
                $buffer[$i - 1] = $buffer[$i - 1] . ' ' . hex2bin('E29C85');
            }
        });

        $i = 0;
        array_walk_recursive($buttonsStruct, function (&$item, $key) use (&$buffer, &$i) {

            if ($key == 'text') {
                $item = $buffer[$i++];
            }
            if ($key == 'callback_data') {
                $item = '#disabledButtonPress';
            }
        });

        $this->writeInfoLog('disableButtonsCallbackOfMessageSetRightAnswer method executing', [
            "butons struct after" => $buttonsStruct,
        ]);

        return $this->sendEditButtonsRequest($chatId, $messageId, $buttonsStruct);
    }

    /** Disables all buttons using sent message's callbacks and markups wrong answer.
     *
     * @param string $chatId
     * @param string $messageId
     * @param array $buttonsStruct
     * @param string $wrongWord
     * @return mixed
     */
    public function disableButtonsCallbackOfMessageSetWrongAnswer(
        string $chatId,
        string $messageId,
        array $buttonsStruct,
        string $wrongWord
    ) {
        $this->writeInfoLog('disableButtonsCallbackOfMessageSetWrongAnswer method executing', [
            'chat id' => $chatId,
            'message id' => $messageId,
            'wrong word' => $wrongWord,
            "buttons struct before" => $buttonsStruct
        ]);

        $i = 0;
        $buffer = [];
        array_walk_recursive($buttonsStruct, function (&$item, $key) use (&$buffer, &$i, $wrongWord) {

            if ($key == 'text') {
                $buffer[$i++] = $item;
            }

            if ($key == 'callback_data' && str_contains($item, $wrongWord)) {
                $buffer[$i - 1] = $buffer[$i - 1] . ' ' . hex2bin('E29D8C');
            }
        });

        $i = 0;
        array_walk_recursive($buttonsStruct, function (&$item, $key) use (&$buffer, &$i) {

            if ($key == 'text') {
                $item = $buffer[$i++];
            }
            if ($key == 'callback_data') {
                $item = '#disabledButtonPress';
            }
        });

        $this->writeInfoLog('disableButtonsCallbackOfMessageSetWrongAnswer method executing', [
            "butons struct after" => $buttonsStruct,
            'buffer' => $buffer
        ]);

        return $this-> sendEditButtonsRequest($chatId, $messageId, $buttonsStruct);
    }

    /** Sets the buttons' struct to cache.
     *
     * @param int $userId
     * @param string $buttonsMessageId
     * @param array $buttonsStruct
     */
    public function setButtonsStructToCache(int $userId, string $buttonsMessageId, array $buttonsStruct)
    {
        $this->writeInfoLog('Setting buttons of message in redis', [
            "user id" => $userId,
            'buttons message id' => $buttonsMessageId,
            'buttonst struct' => $buttonsStruct,
        ]);

        Redis::hSet($userId, $buttonsMessageId, json_encode($buttonsStruct));
    }

    public function getButtonsStructFromCacheAndFree(int $userId, string $buttonsMessageId)
    {
        $result = json_decode(Redis::hGet($userId, $buttonsMessageId), true);

        $this->writeInfoLog('Buttons of message from redis', [
                '$userId' => $userId,
                '$buttonsMessageId' => $buttonsMessageId,
                "result from redis" => $result
            ]);

        Redis::hDel($userId, $buttonsMessageId);
        return $result;
    }

    /** Sends request to telegram for edit the buttons.
     *
     * @param string $chatId
     * @param string $messageId
     * @param array $buttonsStruct
     * @return bool
     */
    private function sendEditButtonsRequest(string $chatId, string $messageId, array $buttonsStruct)
    {
        $request = [
            "chat_id" => $chatId,
            "message_id" => $messageId,
            "reply_markup" => json_encode([
                'inline_keyboard' => $buttonsStruct,
            ])
        ];

        $this->writeInfoLog('Sending edit buttons callback request to telegram', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'buttons struct' => $buttonsStruct,
            'request' => $request,
        ]);

        //endpoint /editMessage only for edit text of message
        $response = Http::get(config('bot.uri_and_token') . '/editMessageReplyMarkup', $request);

        $this->writeInfoLog(
            'Sending edit buttons callback request to telegram. Response',
            $this->logResponseHelper($response)
        );

        return $response->ok();
    }

    /** Sends request to telegram for edit message and buttons.
     *
     * @param string $chatId
     * @param string|null $messageId
     * @param string $text
     * @param array $buttonsStruct
     */
    public function editMessageAndButtons(string $chatId, ?string $messageId, string $text, array $buttonsStruct)
    {
        if (!is_null($messageId)) {
            $request = [
                "chat_id" => $chatId,
                "message_id" => $messageId,
                "text" => $text,
            ];

            $this->writeInfoLog('Sending edit request to telegram', [
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'request' => $request,
            ]);

            $response = Http::get(config('bot.uri_and_token') . '/editMessageText', $request);

            $this->writeInfoLog(
                'Sending edit message request to telegram. Response',
                $this->logResponseHelper($response)
            );

            $this->sendEditButtonsRequest($chatId, $messageId, $buttonsStruct);
        } else {
            $this->writeErrorLog('Failed to send edit request to telegram. Invalid chat message id', [
                'chat_id' => $chatId,
                'message_id' => $messageId
            ]);
        }
    }

    /** Deletes the message from telegram.
     *
     * @param string $chatId
     * @param string|null $messageId
     * @return bool
     */
    public function deleteMessage(string $chatId, ?string $messageId)
    {
        if (!is_null($messageId)) {
            $request = [
                "chat_id" => $chatId,
                "message_id" => $messageId
            ];

            $response = Http::get(config('bot.uri_and_token') . '/deleteMessage', $request);

            $this->writeInfoLog('Has sent delete message request to telegram', [
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'response' => $this->logResponseHelper($response)
            ]);
            return $response->ok();
        } else {
            $this->writeErrorLog('Has not sent delete message request to telegram', [
                'chat_id' => $chatId,
                'message_id' => $messageId,
            ]);
            return false;
        }
    }

    /**Sends message to admin user.
     *
     * @param string $text
     */
    public function sendMessageToAdmin(string $text)
    {
        $adminChatId = config('bot.telegram_admin_id');
        $this->sendMessage($adminChatId, $text);
    }

    /** Helper for logging handle.
     *
     * @param Response $response
     * @param int|null $sentMessageId
     * @return array|mixed
     */
    private function logResponseHelper(Response $response, ?int &$sentMessageId = null)
    {
        $responseLog = [];
        if (!$response->json()['ok']) {
            $responseLog = $response->json();
        } else {
            $responseLog['ok'] = $response->json()['ok'];
            if (isset($response->json()['result']['message_id'])) {
                $sentMessageId = $response->json()['result']['message_id'];
                $responseLog['sent_message_id'] = $sentMessageId;
            }
        }

        return $responseLog;
    }

    /** Get chat id if exists.
     *
     * @return string|null
     */
    public function getChatId()
    {
        try {
            $result = $this?->chatId;
        } catch (\Exception $exception) {
        }
        $this->writeErrorLog('Getting chat id', [
            'chat_id' => $result,
        ]);
        return $result;
    }
}
