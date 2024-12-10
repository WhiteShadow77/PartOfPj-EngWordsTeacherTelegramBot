<?php

namespace App\Services\Cache;

use App\Services\Helpers\FileCacheId;
use App\Traits\LoggerTrait;
use Illuminate\Support\Facades\Redis;

class MessageCacheService
{
    use LoggerTrait;

    public static ?string $filesAttachmentIdentifier = null;
    public static string|int|null $messageId = null;
    public static int $uniqueFakeFileNameAndDirIdentifier = 0;

    public function addToMessage(int|string $messageId, int $doseIndex, string $value)
    {
        Redis::hSet($messageId, $doseIndex, $value);
        $this->writeInfoLog('Added to message method executing using hash', [
            'message id' => $messageId,
            'dose index' => $doseIndex,
            'message' => $value
        ]);
    }

    public function getMessage(int|string $messageId): string
    {
        $result = implode('', Redis::hVals($messageId));
        $this->writeInfoLog('Successfully got hashed message from redis', [
            'message id' => $messageId,
            'message' => $result
        ]);
        return $result;
    }

    public function deleteMessage(int|string $messageId)
    {
        Redis::del($messageId);
        $this->writeInfoLog('Successfully deleted message from redis', [
            'message id' => $messageId
        ]);
    }

    public function cacheMessage(int|string $messageId, string $value)
    {
        Redis::set($messageId, $value);
        $this->writeInfoLog('Successfully cached message using redis set method', [
            'message id' => $messageId,
            'message' => $value
        ]);
    }

    public function getCachedMessage(int|string $messageId)
    {
        $result = Redis::get($messageId);
        $this->writeInfoLog('Successfully got cached message from redis', [
            'message id' => $messageId,
            'message' => $result
        ]);
        return $result;
    }

    public function getMessageUsingSortedSetAndSortByDoseIndex(int|string $messageId): string
    {
        $result = implode('', Redis::zRange($messageId, 0, -1));
        $this->writeInfoLog('Successfully got message from sorted set and sorted by dose index', [
            'message id' => $messageId,
            'message' => $result
        ]);
        return $result;
    }

    public function addToMessageUsingSortedSet(int|string $messageId, int $doseIndex, string $value): void
    {
        Redis::zAdd($messageId, $doseIndex, $value);
        self::$messageId = $messageId;
        $this->writeInfoLog('Added to message with sorting set method executing', [
            'message id' => $messageId,
            'dose index' => $doseIndex,
            'message' => $value
        ]);
    }

    public function addFileNameAndDirToSortedSet(int|string $messageId, ?string $fileNameAndDir, int $doseIndex)
    {
        $this->writeInfoLog('addFileNameAndDirToSortedSet method executing', [
            'message id' => $messageId,
            'self::$messageId' => self::$messageId,
            'file name and dir' => $fileNameAndDir,
            'dose index' => $doseIndex
        ]);

        if (self::$messageId != $messageId || self::$filesAttachmentIdentifier != FileCacheId::make(self::$messageId)) {
            self::$messageId = $messageId;
            self::$filesAttachmentIdentifier = FileCacheId::make(self::$messageId);
        }

        if ($fileNameAndDir == '') {
            $fileNameAndDir = 'fake' . self::$uniqueFakeFileNameAndDirIdentifier++;
        }
        $fileNameAndDir = ($fileNameAndDir == '') ? 'null' : $fileNameAndDir;

        Redis::zAdd(self::$filesAttachmentIdentifier, $doseIndex, $fileNameAndDir);

        $this->writeInfoLog('Added file name and dir to sorted set', [
            'attachment identifier' => self::$filesAttachmentIdentifier,
            'dose index' => $doseIndex,
            'file name and dir' => $fileNameAndDir,
            'message id' => $messageId,
            'self::$messageId' => self::$messageId,
        ]);
        return self::$filesAttachmentIdentifier;
    }

    public function getFileNameAndDirFromHash(?string $filesAttachmentIdentifier)
    {
        $result = Redis::zRange($filesAttachmentIdentifier, 0, -1);
        $this->writeInfoLog('getFileNameAndDirFromHash method', [
            '$filesAttachmentIdentifier' => $filesAttachmentIdentifier,
            'result from hash' => $result
        ]);
        Redis::del($filesAttachmentIdentifier);
        return $result;
    }

    public function addToMessageWithFilePathUsingSortedSet(
        int|string $messageId,
        int $doseIndex,
        string $text,
        string $englishWord,
        ?string $fileNameAndDir = null
    ): void {
        $identifier = $this->addFileNameAndDirToSortedSet($messageId, $fileNameAndDir, $doseIndex);
        $value = json_encode([
            'message' => $text,
            'english_word' => $englishWord,
            'files_attachment_identifier' => $identifier
        ]);

        Redis::zAdd($messageId, $doseIndex, $value);
        $this->writeInfoLog('Added to message with file path using sorting set method', [
            'message id' => $messageId,
            'dose index' => $doseIndex,
            'text' => $text,
            'english word' => $englishWord,
            'message' => $value
        ]);
    }

    public function getMessageWithFilePathUsingSortedSetAndSortByDoseIndex(
        int|string $messageId,
        ?array &$englishWords = null,
        ?array &$attachmentFileNamesAndDirsResult = null
    ): string {
        $message = '';
        $filesAttachmentIdentifier = null;
        $resultFromHash = Redis::zRange($messageId, 0, -1);
        $this->writeInfoLog('getMessageWithFilePathUsingSortedSetAndSortByDoseIndex method, got from hash', [
            'message id' => $messageId,
            'message' => $message,
            'result from hash' => $resultFromHash,
            'attachment file names and dirs' => $attachmentFileNamesAndDirsResult,
            'english_words' => $englishWords
        ]);
        foreach ($resultFromHash as $messageAndEngWordAndIdentifier) {
            $ArrOfMessageAndEngWordAndIdentifier = json_decode($messageAndEngWordAndIdentifier, true);
            $this->writeInfoLog('ArrOfMessageAndEngWordAndIdentifier variable', [
                '$ArrOfMessageAndEngWordAndIdentifier' => $ArrOfMessageAndEngWordAndIdentifier
            ]);
            $message .= $ArrOfMessageAndEngWordAndIdentifier['message'];
            if (
                isset($ArrOfMessageAndEngWordAndIdentifier['files_attachment_identifier']) &&
                isset($ArrOfMessageAndEngWordAndIdentifier['english_word'])
            ) {
                $filesAttachmentIdentifier = $ArrOfMessageAndEngWordAndIdentifier['files_attachment_identifier'];
                $englishWords[] = $ArrOfMessageAndEngWordAndIdentifier['english_word'];
            } else {
                continue;
            }
        }
        $this->writeInfoLog('getMessageWithFilePathUsingSortedSetAndSortByDoseIndex method, got message, attachements, eng. words', [
            'message id' => $messageId,
            'message' => $message,
            'result from hash' => $resultFromHash,
            'files attachments identifiers' => $filesAttachmentIdentifier,
            'attachment file names and dirs' => $attachmentFileNamesAndDirsResult,
            'english words' => $englishWords
        ]);

        $attachmentFileNamesAndDirsResult = $this->getFileNameAndDirFromHash($filesAttachmentIdentifier);

        $this->writeInfoLog('getMessageWithFilePathUsingSortedSetAndSortByDoseIndex method, got result', [
            'message id' => $messageId,
            'message' => $message,
            'attachment file names and dirs' => $attachmentFileNamesAndDirsResult,
            'english words' => $englishWords
        ]);
        return $message;
    }
}
