<?php

namespace App\Services\Commands;

use App\Traits\LoggerTrait;

class SaveLogsCopyCommandService
{
    use LoggerTrait;

    public function __invoke(): void
    {
        $logSourceFile = storage_path('logs') . '/app.log';

        if (!is_dir(storage_path('logs') . '/LogCopies')) {
            mkdir(storage_path('logs') . '/LogCopies');
        }

        $destinationLogFile = storage_path('logs') . '/LogCopies/' . now()->format('Y-m-d_H:i:s_') . 'app.log';
        $fileSize = filesize($logSourceFile);

        if ($fileSize > 0) {
            copy($logSourceFile, $destinationLogFile);

            $this->writeInfoLog('Made copy of log file and saved.', [
                'saved as' => $destinationLogFile,
                'size' => filesize($destinationLogFile)
            ], isAllowedSendToTlg: true);
        } else {
            $this->writeInfoLog('Try to copy empty logs file.', [
                'size' => $fileSize
            ], isAllowedSendToTlg: true);
        }
    }
}
