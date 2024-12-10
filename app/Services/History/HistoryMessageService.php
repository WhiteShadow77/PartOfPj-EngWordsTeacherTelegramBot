<?php

namespace App\Services\History;

use App\Traits\LoggerTrait;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\HistoryMessage;
use App\Models\User;
use App\Enums\MessageType;

class HistoryMessageService
{
    use LoggerTrait;

    public function addRecord(
        string $text,
        int $userId,
        MessageType $messageType,
        ?array $arguments = null
    ) {
        $historyMessageModel = HistoryMessage::create([
            'text' => $text,
            'arguments' => is_null($arguments) ? null : implode('    ', $arguments),
            'type' => $messageType->name,
            'user_id' => $userId
        ]);

        $this->writeInfoLog('Inserted record history_messages table', [
            'text' => $text,
            'arguments' => $arguments,
        ]);

        return $historyMessageModel;
    }

    public function addRecordFromArray(
        array $words,
        int $userId,
        MessageType $messageType,
        ?string $preWordsText = null,
        ?string $preArgumentsText = null,
        ?array $arguments = null
    ) {
        $wordsInString = '';
        $lastWord = end($words);

        foreach ($words as $word) {
            $wordsInString .= trim($word);
            if ($word == $lastWord) {
                $wordsInString .= ' ';
            } else {
                $wordsInString .= '; ';
            }
        }
        $text =  $preWordsText . $wordsInString;

        $historyMessageModel = HistoryMessage::create([
            'text' => $text,
            'arguments' => is_null($arguments) ? null : $preArgumentsText . implode(', ', $arguments),
            'type' => $messageType->name,
            'user_id' => $userId
        ]);

        $this->writeInfoLog('Inserted record history_messages table', [
            'text' => $text,
            'arguments' => $arguments,
        ]);

        return $historyMessageModel;
    }

    public function getAllRecords(int $userId)
    {
        return HistoryMessage::where('user_id', $userId)->get();
    }
}
