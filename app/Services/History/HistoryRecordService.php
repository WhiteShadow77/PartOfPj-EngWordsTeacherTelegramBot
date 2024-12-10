<?php

namespace App\Services\History;

use App\Models\HistoryMessage;
use App\Services\Cache\LanguageCacheService;
use App\Traits\LoggerTrait;
use App\Models\HistoryRecord;
use App\Enums\AnswerKind;
use App\Enums\WordStatus;
use App\Enums\WordKind;
use App\Enums\MessageType;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class HistoryRecordService
{
    use LoggerTrait;

    public function addRecord(
        string $word,
        AnswerKind $answerKind,
        WordStatus $wordStatus,
        WordKind $wordKind,
        int $userId,
        ?int $word_id = null,
        ?string $rightWord = null
    ) {
        $historyRecordModel = HistoryRecord::create([
            'word' => $word,
            'answer_kind' => $answerKind->name,
            'word_status' => $wordStatus->name,
            'word_kind' => $wordKind->name,
            'right_word' => $rightWord,
            'word_id' => $word_id,
            'user_id' => $userId
        ]);

        $this->writeInfoLog('Inserted record history_records table', [
            'word' => $word,
            'answer_kind' => $answerKind->name,
            'word_status' => $wordStatus->name,
            'word_kind' => $wordKind->name,
            'right_word' => $rightWord,
            'word_id' => $word_id,
            'user_id' => $userId
        ]);

        return $historyRecordModel;
    }

    public function getAllRecords(int $userId, HistoryMessageService $messageService)
    {
        $result = HistoryRecord::where('user_id', $userId)->get();
        $this->writeInfoLog('Got all records from history_records table');
        return $result;
    }

    public function getAllRecordsForFrontEnd(int $userId)
    {
        $historyRecordQuery = HistoryRecord::where('user_id', $userId)
            ->selectRaw(
                'id, word, answer_kind, word_status, word_kind, right_word, word_id, user_id, created_at, updated_at'
            );

        $result = HistoryMessage::where('user_id', $userId)
            ->selectRaw(
                'fake as id,
                 text as word,
                 fake as answer_kind,
                 fake as word_status,
                 type as type,
                 arguments as right_word,
                 word_id as word_id,
                 user_id as user_id,
                 created_at,
                 updated_at'
            )
            ->union($historyRecordQuery)
            ->orderBy('created_at', 'DESC')
            ->paginate(18);

        $this->writeInfoLog('Got history records and messages records by union for front-end', [
            'result' => $result
        ]);

        return $result;
    }

    public function isAnswerReceivedAfterDateTime(int $userId, \DateTime $dateTime): bool
    {
        $result = HistoryRecord::where('user_id', $userId)
            ->where('created_at', '>', $dateTime->format('Y-m-d H:i:s'))
            ->take(1)->get();

        $result = (sizeof($result) > 0) ? true : false;

        $this->writeInfoLog('Is answer received after date and time', [
            'user id' => $userId,
            'date and time' => $dateTime->format('Y-m-d H:i:s'),
            'result' => $result
        ]);
        return $result;
    }

    public function deleteByMonthsPeriod(
        int $monthsPeriod,
        int $userId,
        HistoryMessageService $historyMessageService,
        LanguageCacheService $languageCacheService,
        string $chatId
    ) {
        for ($i = 0; $i < $monthsPeriod; $i++) {
            $historyRecordQuery = HistoryRecord::where('user_id', $userId)
                ->selectRaw(
                    'id as record_table_id, answer_kind, word, created_at'
                );

            $minDateFromHistoryRecordAndHistoryMessage = HistoryMessage::where('user_id', $userId)
                ->where('type', '!=', (MessageType::info_deletion)->name)
                ->selectRaw(
                    'fake as record_table_id,
                 id as answer_kind,
                 text as word,
                 created_at'
                )
                ->union($historyRecordQuery)
                ->min('created_at');

            if ($minDateFromHistoryRecordAndHistoryMessage) {
                $endOfMonthDate = (new Carbon($minDateFromHistoryRecordAndHistoryMessage))->endOfMonth();

                $historyRecordQuery = HistoryRecord::where('user_id', $userId)
                    ->selectRaw(
                        'id as record_table_id, answer_kind, word, created_at'
                    )->whereBetween(
                        'created_at',
                        [
                            $minDateFromHistoryRecordAndHistoryMessage,
                            $endOfMonthDate
                        ]
                    );

                $result = HistoryMessage::where('user_id', $userId)
                    ->where('type', '!=', (MessageType::info_deletion)->name)
                    ->selectRaw(
                        'fake as record_table_id,
                 id as answer_kind,
                 text as word,
                 created_at'
                    )->whereBetween(
                        'created_at',
                        [
                            $minDateFromHistoryRecordAndHistoryMessage,
                            $endOfMonthDate
                        ]
                    )
                    ->union($historyRecordQuery)
                    ->orderBy('created_at', 'ASC')
                    ->get()
                    ->toArray();

                $idsToDeleteFromHistoryRecord = [];
                $idsToDeleteFromHistoryMessage = [];

                foreach ($result as $resultItem) {
                    if (!is_null($resultItem['record_table_id'])) {
                        $idsToDeleteFromHistoryRecord[] = $resultItem['record_table_id'];
                    }
                    if (is_numeric($resultItem['answer_kind'])) {
                        $idsToDeleteFromHistoryMessage[] = $resultItem['answer_kind'];
                    }
                }

                HistoryRecord::whereIn('id', $idsToDeleteFromHistoryRecord)->delete();
                HistoryMessage::whereIn('id', $idsToDeleteFromHistoryMessage)->delete();

                $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId($chatId);
                App::setLocale($userLanguage);

                $historyMessageService->addRecord(
                    __("Deleted history from") . ' ' .
                    (new \DateTime($minDateFromHistoryRecordAndHistoryMessage))->format('Y-m-d ') .
                    ' ' . __("lables.for_delete_history_to") . ' ' .
                    (new \DateTime($endOfMonthDate))->format('Y-m-d '),
                    $userId,
                    MessageType::info_deletion
                );

                $this->writeInfoLog('Deleted history by user', [
                    'user id' => $userId,
                    'from date' => $minDateFromHistoryRecordAndHistoryMessage,
                    'to date' => $endOfMonthDate
                ]);
            }
        }
    }

    public function getDeletePeriodParams(int $userId, int $monthsQuantity)
    {
        $historyRecordQuery = HistoryRecord::where('user_id', $userId)
            ->selectRaw(
                'id as record_table_id, answer_kind, word, created_at'
            );

        $minDateFromHistoryRecordAndHistoryMessage = HistoryMessage::where('user_id', $userId)
            ->where('type', '!=', (MessageType::info_deletion)->name)
            ->selectRaw(
                'fake as record_table_id,
                 id as answer_kind,
                 text as word,
                 created_at'
            )
            ->union($historyRecordQuery)
            ->min('created_at');

        if ($minDateFromHistoryRecordAndHistoryMessage) {
            $endDeletionPeriod = (new Carbon($minDateFromHistoryRecordAndHistoryMessage))
                ->addMonths($monthsQuantity - 1);
            $endDeletionPeriodEndOfMonthDate = (new Carbon($endDeletionPeriod))->endOfMonth();

            return [
                'start' => (new \DateTime($minDateFromHistoryRecordAndHistoryMessage))->format('Y-m-d '),
                'end' => (new \DateTime($endDeletionPeriodEndOfMonthDate))->format('Y-m-d ')
            ];
        } else {
            return null;
        }
    }
}
